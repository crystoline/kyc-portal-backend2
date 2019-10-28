<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAgentAPIRequest;
use App\Http\Requests\API\UpdateAgentAPIRequest;
use App\Models\Agent;
use App\Repositories\AgentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Swagger\Annotations as SWG;

/**
 * Class AgentController
 * @package App\Http\Controllers\API
 */

class AgentAPIController extends AppBaseController
{
    /** @var  AgentRepository */
    private $agentRepository;

    public function __construct(AgentRepository $agentRepo)
    {
        $this->agentRepository = $agentRepo;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/agents",
     *      summary="Get a listing of the Agents.",
     *      tags={"Agent"},
     *      description="Get all Agents",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="paginate",
     *          description="Yes to paginate",
     *          type="string",
     *          required=false,
     *          in="query"
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
     *                  @SWG\Items(ref="#/definitions/Agent")
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
        $agents = $this->agentRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($agents->toArray(), 'Agents retrieved successfully');
    }

    /**
     * @param CreateAgentAPIRequest $request
     * @return JsonResponse
     *
     * @SWG\Post(
     *      path="/agents",
     *      summary="Store a newly created Agent in storage",
     *      tags={"Agent"},
     *      description="Store Agent",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Agent that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreatAgentRequest")
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
     *                  ref="#/definitions/Agent"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAgentAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $agent = $this->agentRepository->create($input);

        return $this->sendResponse($agent->toArray(), 'Agent saved successfully');
    }

    /**
     * @param int $id
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/agents/{id}",
     *      summary="Display the specified Agent",
     *      tags={"Agent"},
     *      description="Get Agent",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Agent",
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
     *                  ref="#/definitions/Agent"
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
        /** @var Agent $agent */
        $agent = $this->agentRepository->find($id);

        if ($agent === null) {
            return $this->sendError('Agent not found');
        }

        return $this->sendResponse($agent->toArray(), 'Agent retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAgentAPIRequest $request
     * @return JsonResponse
     *
     * @SWG\Put(
     *      path="/agents/{id}",
     *      summary="Update the specified Agent in storage",
     *      tags={"Agent"},
     *      description="Update Agent",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Agent",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Agent that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreatAgentRequest")
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
     *                  ref="#/definitions/Agent"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAgentAPIRequest $request): JsonResponse
    {
        $input = $request->except(['status']);

        /** @var Agent $agent */
        $agent = $this->agentRepository->find($id);

        if ($agent === null) {
            return $this->sendError('Agent not found');
        }

        $agent = $this->agentRepository->update($input, $id);

        return $this->sendResponse($agent->toArray(), 'Agent updated successfully');
    }

}
