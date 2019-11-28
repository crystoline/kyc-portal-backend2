<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\API\CreateUserAPIRequest;
use App\Http\Requests\API\UpdateUserAPIRequest;
use App\Mail\PasswordResetSuccessful;
use App\Mail\UserProfileCreated;
use App\Models\Agent;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Swagger\Annotations as SWG;

/**
 * Class UserController
 * @package App\Http\Controllers\Api
 */

class UserController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/users",
     *      summary="Get a listing of the Users.",
     *      tags={"User Management"},
     *      description="Get all Users",
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
     *                  @SWG\Items(ref="#/definitions/User")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $users = $this->userRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($users->toArray(), 'Users retrieved successfully');
    }

    /**
     * @param CreateUserAPIRequest $request
     * @return JsonResponse
     *
     * @SWG\Post(
     *      path="/users",
     *      summary="Store a newly created User in Database",
     *      tags={"User Management"},
     *      description="Create new user",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
    *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="User that should be registered",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/CreateUserRequest")
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
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateUserAPIRequest $request): JsonResponse
    {
        try {
            $input = $request->except(['password', 'status']);
            /** @var User $user */
            $user = $this->userRepository->create(array_merge($input, ['password' => random_int(10000, 99999)]));
            $host = $request->getHost();

                  Mail::to($user->email)
                      ->send(new UserProfileCreated($user, $host));
            return $this->sendResponse($user->toArray(), 'User profile was created');
        } catch (\Exception $e) {
            $this->sendError('Could not create user profile', '500');
        }

    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/users/{id}",
     *      summary="Display the specified User",
     *      tags={"User Management"},
     *      description="Get User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
    *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of User",
     *          type="integer",
     *          required=true,
     *          in="path"
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
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id): JsonResponse
    {
        /** @var User $user */
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            return $this->sendError('User not found');
        }

        return $this->sendResponse($user->toArray(), 'User retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateUserAPIRequest $request
     * @return JsonResponse
     *
     * @SWG\Put(
     *      path="/users/{id}",
     *      summary="Update the specified User in storage",
     *      tags={"User Management"},
     *      description="Update User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
    *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of User",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="User that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreateUserRequest")
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
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateUserAPIRequest $request): JsonResponse
    {
        $input = $request->except(['password', 'status']);

        /** @var User $user */
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $user = $this->userRepository->update($input, $id);
        //$user->load('group');
        return $this->sendResponse($user->toArray(), 'User updated successfully');
    }

    /**
     * @param int $id
     * @return JsonResponse
     *
     * @SWG\Post(
     *      path="/users/{id}/toggle-status",
     *      summary="Toggle user status",
     *      tags={"User Management"},
     *      description="",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
    *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of User",
     *          type="integer",
     *          required=true,
     *          in="path"
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
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function toggleStatus($id): JsonResponse
    {

        /** @var User $user */
        $user = $this->userRepository->find($id);

        if ($user === null) {
            return $this->sendError('User not found');
        }
        try{
            $user = $this->userRepository->update(['status' => $user->status? 0: 1], $id);
            return $this->sendResponse($user->toArray(),  'User account has been '.($user->status? 'enabled': 'disabled'));
        }catch (\Exception $exception){
        }
        return $this->sendError('Could not change user status');
        //$user->load('group');
    }

    /**
     * @SWG\Post(
     *      path="/users/assign-agents",
     *      summary="Assign Agents to a field officer",
     *      tags={"User Management"},
     *      description="",
     *      produces={"application/json", "application/xml"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *           @SWG\Schema(
     *               @SWG\Parameter(
     *                   name="user_id",
     *                   description="id of User",
     *                   type="integer",
     *                   required=true
     *               ),
     *               @SWG\Property(
     *                   property="agent_ids",
     *                   type="array",
     *                   @SWG\Items(
     *                       type="number",
     *                        default=0
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
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function assignAgents(Request $request): JsonResponse
    {
        $this->validate($request, [
            'agent_ids' => 'required',
            'agent_ids.*' => 'required',
            'user_id'=> 'required|exists:users,id'
        ], [
                'agent_ids.*.required' => 'Required at least 1 agent id/code'
        ]);
        $officer = User::query()->whereHas('group', static function(Builder $query){
           // dd($query);
                $query->where('role', setting('field_officer_role','field_officer'));
        })->find( $request->input('user_id'));

        if(!$officer){
            return $this->sendError('User is not a field officer', 403);
        }

        $affected = Agent::query()
            ->WhereIn('id', $request->input('agent_ids'))
            //->orwhereIn('code', $request->input('agent_ids'))
            ->update( $request->only('user_id'));
        return $this->sendResponse(null,  "{$affected} agent(s) was assigned to officer");

    }

}
