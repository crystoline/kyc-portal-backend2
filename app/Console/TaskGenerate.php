<?php

namespace App\Console;

//use Illuminate\Console\Command;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Util\Sql;
use Illuminate\Support\Str;

class TaskGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:generate {--connection= : Specify a connections} 
    {--namespace= : Specify a namespace to match from App\Http\Controllers}
    {--base_namespace= : Specify base namespace to controller root}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates tasks and seeds the modules and tasks table';

    private static $excluded_modules;

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();

        static::$excluded_modules = config('task-generator.excluded_modules',[]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $task_list = DB::connection($this->option('connection'))->table('tasks')->pluck('route','id');

       /* $auth_task_list = DB::connection($this->option('connection'))->table('authorizations')->whereNotNull('task_id')->pluck('task_id','id');
        if(!empty($auth_task_list))
            DB::connection($this->option('connection'))->table('authorizations')->whereNotNull('task_id')->update(['task_id'=>null]);*/

        $sql = new Sql();
        DB::connection($this->option('connection'))->statement($sql->disableChecks);

        $perm_task_list = DB::connection($this->option('connection'))->table('permissions')->select('id','group_id','task_id')->get();
        if(!empty($perm_task_list)){
            DB::connection($this->option('connection'))->statement('TRUNCATE TABLE permissions');
        }

       /* $perm_auth_task_list = DB::connection($this->option('connection'))->table('permission_authorizers')->select('id','group_id','task_id')->get();
        if(!empty($perm_auth_task_list)){
            DB::connection($this->option('connection'))->statement('TRUNCATE TABLE permission_authorizers');
        }*/

        //Lets cache these values incase a failure occurs
        if(Cache::get('temp.task_list')) {
            $task_list = Cache::get('temp.task_list');
        }
        else {
            Cache::forever('temp.task_list', $task_list);
        }

//        if(Cache::get('temp.auth_task_list')) $auth_task_list = Cache::get('temp.auth_task_list');
//        else Cache::forever('temp.auth_task_list',$auth_task_list);

        if(Cache::get('temp.perm_task_list')) $perm_task_list = Cache::get('temp.perm_task_list');
        else Cache::forever('temp.perm_task_list',$perm_task_list);

        //if(Cache::get('temp.perm_auth_task_list')) $perm_auth_task_list = Cache::get('temp.perm_auth_task_list');
       // else Cache::forever('temp.perm_auth_task_list',$perm_auth_task_list);

        DB::connection($this->option('connection'))->table('tasks')->delete();
        $sql->resetAutoIncrement('tasks');

        DB::connection($this->option('connection'))->table('modules')->delete();
        $sql->resetAutoIncrement('modules');

        DB::connection($this->option('connection'))->statement($sql->enableChecks);

        $object=Route::getRoutes();

        $pattern = ['/destroy/', '/index/', '/create/', '/store/', '/(^me$)/'];
        $replacement = ['delete', 'list', 'create', '/register/', 'my'];

        $task_order = [null=>1];
        $parent_task = [];
        $count = 0;

        $task_permit = config('task-generator.middleware_scopes',[]);

        if(!$base_namespace = $this->option('base_namespace')){
            $base_namespace = config('task-generator.base_namespace','');
        }

        if($namespaces = $this->option('namespace')){
            $namespaces = explode(',',$namespaces);
        }

        foreach ($object as $value) {

            $route = $value->getName();

            if(!empty($task_permit) && empty(array_intersect($task_permit,$value->middleware()))) {
                continue;
            }

            if(!$route) {
                continue;
            }

            $method = $value->methods[0];
            if(!$method == "PATCH") {
                continue;
            }

            $controller = explode("@",$value->getActionName());

            if(!isset($controller[1])) {
                continue;
            }

            $controller_name = $controller[0];
            $controller_method = $controller[1];

            try{
                if (!in_array($controller_method, get_class_methods($controller_name))) {
                    continue;
                }
            }
            catch(\Exception $e){
                die($controller_name." does not exist. on line ". $e->getLine());
            }

            if(!empty($namespaces)){
                $allowed = false;
                foreach ($namespaces as $namespace){
                    if(strstr($controller_name,"App\\Http\\Controllers\\".$namespace)){
                        $allowed = true;
                        break;
                    }
                }
                if(!$allowed) {
                    continue;
                }
            }

            if(!empty(trim($base_namespace,"\\"))) {
                $name = str_replace("App\\Http\\Controllers\\" . trim($base_namespace, "\\") . "\\", '', $controller_name);
            }
            else {
                $name = str_replace("App\\Http\\Controllers\\", '', $controller_name);
            }

            $names = explode("\\",$name);
            $controller_name = $names[count($names) - 1];
            unset($names[count($names) - 1]);

            $mn = implode('-',$names);

            if(in_array($mn,static::$excluded_modules)) {
                continue;
            }

            $module_name = $module_id = null;
            $i = 0;
            if(count($names) > 0){

                $exist = (array) DB::connection($this->option('connection'))->table('modules')->where('name',$mn)->first();

                if(empty($exist)) {
                    $module_name = $mn;
                    $module_id = DB::connection($this->option('connection'))->table('modules')->insertGetId([
                        'name'=> $module_name,
                        'description'=> $module_name. ' Module',
                        'visibility'=>'1',
                        'order'=>$i,
                        'icon'=>'fa fa-circle-o',
                    ]);
                    $task_order[$module_id] = 1;
                }
                else{
                    $module_id = $exist['id'];
                    $module_name = $exist['name'];
                }

            }
            else {
                $module_id =null;
                $module_name = null;
            };

            $order = $task_order[$module_id];

            $cname = str_replace('Controller', '',$controller_name);
            $controller_method = preg_replace($pattern,$replacement,$controller_method);
            $name = self::normalCase($controller_method. ' ' .$cname);
            $task_type = ($method == "PUT" or $method == "DELETE" or !empty($value->parameterNames()))? '1' : '0';

            $task_id = DB::connection($this->option('connection'))->table('tasks')->insertGetId(
                [
                    'module_id' => $module_id,
                    'route' => $route,
                    'name' => str_replace(' Api', '',$name),
                    'task_type' => $task_type,
                    'description' => str_replace(' Api','', static::getTaskName($name,$controller_method,$cname,$module_name,$method)),
                    'visibility' => 1,
                    'order' => $order,
                ]
            );

            if(false !== strpos($route, 'index')){
                $routeArray = explode('.',$route);
                array_pop($routeArray);
                $parent_task[implode('.',$routeArray)] = $task_id;
            }

            $task_order[$module_id]++;
            $count++;
        }

        foreach($parent_task as $k=>$id){
            DB::connection($this->option('connection'))->table('tasks')->where('route','like',$k."%")->where('id','<>',$id)->update(['parent_task_id'=>$id]);
        }

        print "\n";

        $new_task_list = DB::connection($this->option('connection'))->table('tasks')->pluck('id','route');

        /*if(!empty($auth_task_list)){
            print "Remapping ".(count($auth_task_list))." authorization task(s)";

            foreach($auth_task_list as $id=>$task_id){
                if(isset($task_list[$task_id]) and isset($new_task_list[$task_list[$task_id]]))
                    DB::connection($this->option('connection'))->table('authorizations')->where('id',$id)->update(['task_id'=>$new_task_list[$task_list[$task_id]]]);
            }
            Cache::forget('temp.auth_task_list');
        }*/

        if(!empty($perm_task_list)){

            $this->info('Regenerating ' . count($perm_task_list) . ' permissions. ');

            $data = [];
            foreach($perm_task_list as $row){
                if(isset($task_list[$row->task_id], $new_task_list[$task_list[$row->task_id]])) {
                    $data[] = ['group_id' => $row->group_id, 'task_id' => $new_task_list[$task_list[$row->task_id]], 'created_at' => now(), 'updated_at' => now()];
                }
            }

            DB::connection($this->option('connection'))->table('permissions')->insert($data);
            Cache::forget('temp.perm_task_list');
        }

        if(!empty($perm_auth_task_list)){
            $this->info('Regenerating ' . count($perm_auth_task_list) . ' authorizer permissions');

            $data = [];
            foreach($perm_auth_task_list as $row){
                if(isset($task_list[$row->task_id]) and isset($new_task_list[$task_list[$row->task_id]]))
                    $data[] = ['group_id'=>$row->group_id,'task_id'=>$new_task_list[$task_list[$row->task_id]],'created_at'=>now(),'updated_at'=>now()];
            }

            DB::connection($this->option('connection'))->table('permission_authorizers')->insert($data);
            Cache::forget('temp.perm_auth_task_list');
        }

        Cache::forget('temp.task_list');

        $this->info((count($task_order) - 1)."module(s) and {$count} task(s) were generated");
    }

    public static function getTaskName($name,$controller_method,$controller,$module,$method){

        $controller_method = self::normalCase($controller_method);

        $nouns = ['staff','beneficiar','biller','merchant','consumer'];

        $noun = array_shift($nouns);
        $is_noun = strstr($controller_method,$noun);
        foreach ($nouns as $noun){
            $is_noun = ($is_noun or strstr($controller_method,$noun));
        }

        if($method == "DELETE"){
            $prefix = "Delete";
        }
        elseif($method == "PUT"){
            $prefix = "Update";
        }
        else{
            $prefix = "View";
        }

        if($module == "Report"){
            if($is_noun){
                return ucwords($prefix." ".$controller_method." ".$module);
            }
            else{
                if(strtolower($controller_method) == "list"){
                    $controller_method = "View";
                    $controller = Str::plural($controller);
                }
                elseif(strtolower($controller_method) == "show"){
                    $module = $module." Detail";
                }
                elseif($controller == "User"){
                    return ucwords($controller." ".$controller_method." ".$module);
                }

                return ucwords($controller_method." ".$controller." ".$module);
            }
        }

        if(strtolower($controller_method) == "list"){
            $controller = Str::plural($controller);
            $name = str_replace(array('List ', $controller), array('View ', $controller), $name);
        }
        elseif(strtolower($controller_method) == "show"){
            $name = $name." Detail";
        }

        return $name;
    }
    /**
     * @param string $str
     *
     * @return string
     */
    public static function normalCase($str): string
    {
        $str = preg_replace(['/(App\\\)+/', '/(:|-|_|\(|\))/'], ['', ' $1 '], $str);

        $strings = explode(' ', $str);
        $new_strings = [];
        foreach ($strings as $string) {
            if (strtoupper($string) != $string and ($sc = snake_case($string)) != strtolower($string)) {
                $new_strings[] = trim(ucwords(str_replace('_', ' ', $sc)));
                continue;
            }

            $new_strings[] = $string;
        }

        return ucfirst(preg_replace(['/ (:|-|_|\(|\)) /', '/_/'], ['$1', ' '], implode(' ', $new_strings)));
    }
}
