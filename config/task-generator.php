<?php
/**
 * Created by PhpStorm.
 * User: cryst
 * Date: 19/03/17
 * Time: 8:47 AM
 */

return [
    'default_task_model' => '',
    'default_module_model' => '',
    'excluded_modules' => [ 'Auth', '\\Laravel\\Passport\\Http\\Controllers', '\\Appointer\\Swaggervel\\Http\\Controllers'],
   'middleware_scopes' => ['auth:api'],
   // 'base_namespace' => 'App\Http\Controllers\Api'
];