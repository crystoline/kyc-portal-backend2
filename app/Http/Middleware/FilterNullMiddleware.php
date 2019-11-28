<?php

namespace App\Http\Middleware;

use Closure;

class FilterNullMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $all = $request->all() ;
        self::filterNullInput($all);
        return $next($request);
    }
    private static function filterNullInput(array $data, $parent = null): void
    {
        //die(json_encode($data));
        foreach ($data as $key => $value){
            $key = $parent !== null? "{$parent}.{$key}": $key;
            //die(print_r($key, true));
            if(request()->has($key)){
                if(is_array($value)){
                    self::filterNullInput($value, $key);
                }
                elseif(request()->input($key) == null){
                    request()->request->remove($key);
                }
            }
        }

    }

}
