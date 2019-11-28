<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\API\CreateGroupAPIRequest;
use App\Http\Requests\API\UpdateGroupAPIRequest;
use App\Models\Group;
use App\Repositories\GroupRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class GroupController
 * @package App\Http\Controllers\Api
 */

class GroupController extends AppBaseController
{
    /** @var  GroupRepository */
    private $groupRepository;

    public function __construct(GroupRepository $groupRepo)
    {
        $this->groupRepository = $groupRepo;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/groups",
     *      summary="Get a listing of the Groups.",
     *      tags={"Group"},
     *      description="Get all Groups",
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
     */
    public function index(Request $request): JsonResponse
    {
        $groups = $this->groupRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($groups->toArray(), 'Groups retrieved successfully');
    }

    /**
     * @param CreateGroupAPIRequest $request
     * @return JsonResponse
     *
     * @SWG\Post(
     *      path="/groups",
     *      summary="Store a newly created Group in storage",
     *      tags={"Group"},
     *      description="Store Group",
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
     *          description="Group that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreateGroupRequest")
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
     *                  ref="#/definitions/Group"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateGroupAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $group = $this->groupRepository->create($input);

        return $this->sendResponse($group->toArray(), 'Group saved successfully');
    }

    /**
     * @param int $id
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/groups/{id}",
     *      summary="Display the specified Group",
     *      tags={"Group"},
     *      description="Get Group",
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
     *          description="id of Group",
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
     *                  ref="#/definitions/Group"
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
        /** @var Group $group */
        $group = $this->groupRepository->find($id);

        if ($group === null) {
            return $this->sendError('Group not found');
        }

        return $this->sendResponse($group->toArray(), 'Group retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateGroupAPIRequest $request
     * @return JsonResponse
     *
     * @SWG\Put(
     *      path="/groups/{id}",
     *      summary="Update the specified Group in storage",
     *      tags={"Group"},
     *      description="Update Group",
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
     *          description="id of Group",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Group that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreateGroupRequest")
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
     *                  ref="#/definitions/Group"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateGroupAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Group $group */
        $group = $this->groupRepository->find($id);

        if ($group === null) {
            return $this->sendError('Group not found');
        }

        $group = $this->groupRepository->update($input, $id);

        return $this->sendResponse($group->toArray(), 'Group updated successfully');
    }

}
