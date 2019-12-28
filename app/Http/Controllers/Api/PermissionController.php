<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Group;
use App\Models\Permission;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Swagger\Annotations as SWG;

/**
 * Class PermissionController
 * @package App\Http\Controllers\Api
 */
class PermissionController extends AppBaseController
{
    /**
     * @SWG\Get(
     *      path="/api/v1/permissions",
     *      summary="Get all permission.",
     *      tags={"Permissions"},
     *      description="Get all permissions",
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
     *                  @SWG\Items(ref="#/definitions/Group")
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

    public function index(): JsonResponse
    {
        return $this->sendResponse(Group::query()->with('tasks')->get(), '');
    }

    /**
     * @SWG\Put(
     *      path="/api/v1/permissions/{group}",
     *      summary="Update permissions",
     *      tags={"Permissions"},
     *      description="Update permissions",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          name="group",
     *          description="id of Group",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *           name="body",
     *           in="body",
     *           required=true,
     *           @SWG\Schema(
     *               @SWG\Property(
     *                   property="task_ids",
     *                   type="array",
     *                   @SWG\Items(
     *                       type="integer"
     *                   )
     *               )
     *           )
     *      ),
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
     *                  @SWG\Items(ref="#/definitions/Group")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @param Group $group
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Group $group, Request $request): JsonResponse
    {

        $ids = $request->input('task_ids', []);
        $this->validate($request, [
            'task_ids' => 'required',
            'task_ids.*' => 'required|exists:tasks,id'
        ]);
        $group->tasks()->sync($ids);
        return $this->sendResponse($group->load('tasks'), Artisan::output());
    }

    /**
     * @SWG\Post(
     *      path="/api/v1/permissions/tasks",
     *      summary="Get all tasks.",
     *      tags={"Permissions"},
     *      description="Get all tasks",
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
     *                  @SWG\Items(ref="#/definitions/Task")
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
    public function tasks(): JsonResponse
    {
        return $this->sendResponse(Task::query()->get(), '');
    }

    /**
     * @SWG\Post(
     *      path="/api/v1/permissions/generate-tasks",
     *      summary="Re-generate tasks.",
     *      tags={"Permissions"},
     *      description="Re-generate tasks",
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
     *                  @SWG\Items(ref="#/definitions/Group")
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
    public function generateTasks(): JsonResponse
    {
        Artisan::call('task:generate');
        return $this->sendResponse(null, Artisan::output());
    }

    /**
     * @SWG\Post(
     *      path="/api/v1/permissions/default",
     *      summary="Set default permission.",
     *      tags={"Permissions"},
     *      description="Set default permission",
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
     *                  @SWG\Items(ref="#/definitions/Group")
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
    public function defaultPermissions(): JsonResponse
    {
        $default = Config::get('permission.default');
       // return  $this->sendResponse($default, []);
        if (is_array($default)) {
            $groups = new Collection();
            foreach ($default as $role => $routes) {
                /** @var Group $group */
                $group = Group::query()->where('role', $role)->first();
                $task_ids = Task::query()->whereIn('route', $routes)->get()->pluck('id');
                if ($group !== null) {
                    $group->tasks()->sync($task_ids);
                    $group->load('tasks');
                    $groups->push($group);
                }
            }
            return $this->sendResponse($groups, 'Permissions updated to default successfully');
        }
        return $this->sendError('Could not updated permissions ');
    }
}
