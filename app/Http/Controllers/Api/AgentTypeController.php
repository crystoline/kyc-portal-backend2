<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\API\CreateAgentTypeAPIRequest;
use App\Http\Requests\API\UpdateAgentTypeAPIRequest;
use App\Models\AgentType;
use App\Repositories\AgentTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class AgentTypeController
 * @package App\Http\Controllers\Api
 */

class AgentTypeController extends AppBaseController
{
    /** @var  AgentTypeRepository */
    private $agentTypeRepository;

    public function __construct(AgentTypeRepository $agentTypeRepo)
    {
        $this->agentTypeRepository = $agentTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/api/v1/agentTypes",
     *      summary="Get a listing of the AgentTypes.",
     *      tags={"AgentType"},
     *      description="Get all AgentTypes",
     *      produces={"application/json"},
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
     *                  @SWG\Items(ref="#/definitions/AgentType")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $agentTypes = $this->agentTypeRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($agentTypes->toArray(), 'Agent Types retrieved successfully');
    }

    /**
     * @param CreateAgentTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/api/v1/agentTypes",
     *      summary="Store a newly created AgentType in storage",
     *      tags={"AgentType"},
     *      description="Store AgentType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AgentType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AgentType")
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
     *                  ref="#/definitions/AgentType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAgentTypeAPIRequest $request)
    {
        $input = $request->all();

        $agentType = $this->agentTypeRepository->create($input);

        return $this->sendResponse($agentType->toArray(), 'Agent Type saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/api/v1/agentTypes/{id}",
     *      summary="Display the specified AgentType",
     *      tags={"AgentType"},
     *      description="Get AgentType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AgentType",
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
     *                  ref="#/definitions/AgentType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var AgentType $agentType */
        $agentType = $this->agentTypeRepository->find($id);

        if (empty($agentType)) {
            return $this->sendError('Agent Type not found');
        }

        return $this->sendResponse($agentType->toArray(), 'Agent Type retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAgentTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/api/v1/agentTypes/{id}",
     *      summary="Update the specified AgentType in storage",
     *      tags={"AgentType"},
     *      description="Update AgentType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AgentType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AgentType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AgentType")
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
     *                  ref="#/definitions/AgentType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAgentTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var AgentType $agentType */
        $agentType = $this->agentTypeRepository->find($id);

        if (empty($agentType)) {
            return $this->sendError('Agent Type not found');
        }

        $agentType = $this->agentTypeRepository->update($input, $id);

        return $this->sendResponse($agentType->toArray(), 'AgentType updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/api/v1/agentTypes/{id}",
     *      summary="Remove the specified AgentType from storage",
     *      tags={"AgentType"},
     *      description="Delete AgentType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AgentType",
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
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var AgentType $agentType */
        $agentType = $this->agentTypeRepository->find($id);

        if (empty($agentType)) {
            return $this->sendError('Agent Type not found');
        }

        $agentType->delete();

        return $this->sendResponse($id, 'Agent Type deleted successfully');
    }
}
