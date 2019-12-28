<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends AppBaseController
{
    /**
     * @SWG\Get(
     *      path="/api/v1/dashboard/all_users",
     *      summary="Total users",
     *      tags={"Dashboard"},
     *      description="Total users",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  @SWG\Property(
     *                      property="total",
     *                      type="integer"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @return JsonResponse
     */
    public function totalUsers(): JsonResponse
    {
        return $this->sendResponse([
            'total' =>User::query()->count()
        ],'');
    }
    /**
     * @SWG\Get(
     *      path="/api/v1/dashboard/all_users_by_group",
     *      summary="Total users by group",
     *      tags={"Dashboard"},
     *      description="Total users by group",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
 *                      @SWG\Items(
 *                          @SWG\Property(
 *                              property="total",
 *                              type="integer"
 *                          ),
 *                          @SWG\Property(
 *                              property="group_name",
 *                              type="string"
 *                          )
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @return JsonResponse
     */
    public function totalUsersByGroup(): JsonResponse
    {
         $data =  User::query() ->selectRaw('COUNT(users.id) AS total, groups.name AS group_name')
            ->leftJoin('groups', 'groups.id', 'users.group_id')
            ->groupBy(['users.group_id'])->get();
        return $this->sendResponse($data,'');
    }
    /**
     * @SWG\Get(
     *      path="/api/v1/dashboard/all_agents",
     *      summary="Total Agents",
     *      tags={"Dashboard"},
     *      description="Total Agents",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  @SWG\Property(
     *                      property="total",
     *                      type="integer"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @return JsonResponse
     */
    public function totalAgent(): JsonResponse
    {
        return $this->sendResponse([
            'total' =>Agent::query()->count()
        ],'');
    }
    /**
     * @SWG\Get(
     *      path="/api/v1/dashboard/all_principal_agents",
     *      summary="Total users",
     *      tags={"Dashboard"},
     *      description="Total principal agents",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  @SWG\Property(
     *                      property="total",
     *                      type="integer"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @return JsonResponse
     */
    public function principalAgents(): JsonResponse
    {
        return $this->sendResponse([
            'total' =>Agent::query()->whereNotNull('parent_agent_id')->count()
        ],'');
    }
    /**
     * @SWG\Get(
     *      path="/api/v1/dashboard/all_sole_agents",
     *      summary="Total sole agents",
     *      tags={"Dashboard"},
     *      description="Total sole agents",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  @SWG\Property(
     *                      property="total",
     *                      type="integer"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @return JsonResponse
     */
    public function soleAgents(): JsonResponse
    {
        return $this->sendResponse([
            'total' =>Agent::query()->whereNull('parent_agent_id')->count()
        ],'');
    }
    /**
     * @SWG\Get(
     *      path="/api/v1/dashboard/pending_verification",
     *      summary="Total pending verifications",
     *      tags={"Dashboard"},
     *      description="Total pending verifications",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  @SWG\Property(
     *                      property="total",
     *                      type="integer"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @return JsonResponse
     */
    public function pendingVerifications(): JsonResponse
    {
        return $this->sendResponse([
            'total' => Verification::query()->where('status', 9)->count()
        ],'');
    }

    /**
     * @SWG\Get(
     *      path="/api/v1/dashboard/monthly_verifications",
     *      summary="monthly verifications",
     *      tags={"Dashboard"},
     *      description="monthly verifications",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      @SWG\Property(
     *                          property="total",
     *                          type="integer"
     *                      ),
     *                      @SWG\Property(
     *                          property="month_name",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="year",
     *                          type="integer"
     *                      )
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @return JsonResponse
     */
    public function monthlyVerification(): JsonResponse
    {
        $data =  Verification::query()
            ->selectRaw("COUNT(id) AS total, DATE_FORMAT(created_at, '%b') as month_name,DATE_FORMAT(created_at, '%Y') as year")
            ->where('created_at', '>=', Carbon::create()->subMonths(6)->format('Y-m-01'))
            // ->where('status', 1)
            ->groupBy(['month_name'])
            ->orderBy('created_at', 'ASC')
            ->get()->toArray();
        return $this->sendResponse($data,'');
    }
    /**
     * @SWG\Get(
     *      path="/api/v1/dashboard/all_monthly_verifications",
     *      summary="all monthly verifications",
     *      tags={"Dashboard"},
     *      description="all monthly verifications",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      @SWG\Property(
     *                          property="month_name",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="year",
     *                          type="integer"
     *                      ),
     *                      @SWG\Property(
     *                          property="approved",
     *                          type="integer"
     *                      ),
     *                      @SWG\Property(
     *                          property="not_approved",
     *                          type="integer"
     *                      ),
     *                      @SWG\Property(
     *                          property="pending",
     *                          type="integer"
     *                      ),
     *                      @SWG\Property(
     *                          property="in_complete",
     *                          type="integer"
     *                      )
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @return JsonResponse
     */
    public function monthlyAllVerifications(): JsonResponse
    {
        $data = Verification::query()->addSelect([
            'month_name' => DB::raw("DATE_FORMAT(created_at, '%b') AS month_name"),
            'year' => DB::raw("DATE_FORMAT(created_at, '%Y') AS year"),
            'approved' =>  Verification::query()->selectRaw('COUNT(id)')
                ->where([
                    [  DB::raw("DATE_FORMAT(created_at, '%b')") ,  DB::raw('month_name')],
                    [ 'status', 1],
                    ['created_at', '>=', Carbon::create()->subMonths(6)->format('Y-m-01')]
                ]),
            'not_approved' =>  Verification::query()->selectRaw('COUNT(id)')
                ->where([
                    [  DB::raw("DATE_FORMAT(created_at, '%b')") ,  DB::raw('month_name')],
                    [ 'status', 0],
                    ['created_at', '>=', Carbon::create()->subMonths(6)->format('Y-m-01')]
                ]),
            'pending' =>  Verification::query()->selectRaw('COUNT(id)')
                ->where([
                    [  DB::raw("DATE_FORMAT(created_at, '%b')") ,  DB::raw('month_name')],
                    [ 'status', 9],
                    ['created_at', '>=', Carbon::create()->subMonths(6)->format('Y-m-01')]
                ]),
            'in_complete' =>  Verification::query()->selectRaw('COUNT(id)')
                ->where([
                    [  DB::raw("DATE_FORMAT(created_at, '%b')") ,  DB::raw('month_name')],
                    [ 'status', 2],
                    ['created_at', '>=', Carbon::create()->subMonths(6)->format('Y-m-01')]
                ])
        ])->where('created_at', '>=', Carbon::create()->subMonths(6)->format('Y-m-01'))
            ->groupBy(['month_name'])
            ->orderBy('created_at', 'ASC')
            ->get();

        return $this->sendResponse($data,'');
    }

    /**
     * @SWG\Get(
     *      path="/api/v1/dashboard/all_verification_periods",
     *      summary="all verification Periods",
     *      tags={"Dashboard"},
     *      description="all verifications Period",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      @SWG\Property(
     *                          property="title",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="date_start",
     *                          type="date-time"
     *                      ),
     *                      @SWG\Property(
     *                          property="approved",
     *                          type="integer"
     *                      ),
     *                      @SWG\Property(
     *                          property="not_approved",
     *                          type="integer"
     *                      ),
     *                      @SWG\Property(
     *                          property="pending",
     *                          type="integer"
     *                      ),
     *                      @SWG\Property(
     *                          property="in_complete",
     *                          type="integer"
     *                      )
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @return JsonResponse
     */
    public function byVerifictionExcercise(){
        $data = Verification::query()->addSelect([
            'verification_periods.title',
            'verification_periods.date_start',
            'approved' =>  Verification::query()->selectRaw('COUNT(id)')
                ->where([
                    [ 'status', 1],
                    ['verifications.verification_period_id', DB::raw('verification_periods.id')]
                ]),
            'not_approved' =>  Verification::query()->selectRaw('COUNT(id)')
                ->where([
                    [ 'status', 0],
                    ['verifications.verification_period_id', DB::raw('verification_periods.id')]
                ]),
            'pending' =>  Verification::query()->selectRaw('COUNT(id)')
                ->where([
                    [ 'status', 9],
                    ['verifications.verification_period_id', DB::raw('verification_periods.id')]
                ]),
            'in_complete' =>  Verification::query()->selectRaw('COUNT(id)')
                ->where([
                    [ 'status', 2],
                    ['verifications.verification_period_id', DB::raw('verification_periods.id')]
                ])
        ])
            ->leftJoin('verification_periods', 'verification_periods.id', 'verifications.verification_period_id')
            ->groupBy(['verification_periods.id'])
            ->orderBy('date_start', 'ASC')
            ->get();

        return $this->sendResponse($data,'');
    }
}
