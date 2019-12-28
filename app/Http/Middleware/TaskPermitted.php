<?php

namespace App\Http\Middleware;

use App\Models\Task;
use App\Models\User;
use Closure;

class TaskPermitted
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

        /** @var User $user */
        $user = auth()->user();
        $route = $request->route()->getName();
        /** @var Task $task */
        $task = Task::query()->where('route', $route)->first();

        if( $user !== null && ($task === null || $user->id === 1 || $user->hasPermission($route))){
            return $next($request);
        }
        return abort(403, 'Access denied to this resource');
    }
}
