<?php

namespace App\Http\Middleware;

use App\Models\Agent;
use Closure;

class CreateVerificationMiddleware
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

        if ($request->has('parent_agent_id')){


            if( !is_int($request->input('parent_agent_id'))) {
                /** @var Agent $agent */
                $agent = Agent::query()->where('code', $request->input('parent_agent_id'))->first();
                if($agent !== null){
                    $request->merge(['parent_agent_id' => $agent->id]);
                }
            }
        }

        if($request->input('code')){
            /** @var Agent $agent */
            $agent = Agent::query()->where('code', $request->input('code'))->first();
            if($agent !== null){
                $request->merge(['agent_id' => $agent->id]);
            }
        }


        return $next($request);
    }
}
