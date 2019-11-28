<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTerritoryAPIRequest;
use App\Http\Requests\API\UpdateTerritoryAPIRequest;
use App\Models\Territory;
use App\Repositories\TerritoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class TerritoryController
 * @package App\Http\Controllers\Api
 */

class TerritoryAPIController extends AppBaseController
{
    /** @var  TerritoryRepository */
    private $territoryRepository;

    public function __construct(TerritoryRepository $territoryRepo)
    {
        $this->territoryRepository = $territoryRepo;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/territories",
     *      summary="Get a listing of the Territories.",
     *      tags={"Territory"},
     *      description="Get all Territories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
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
     *                  @SWG\Items(ref="#/definitions/Territory")
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
        $territories = $this->territoryRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($territories->toArray(), 'Territories retrieved successfully');
    }

    /**
     * @param CreateTerritoryAPIRequest $request
     * @return JsonResponse
     *
     * @SWG\Post(
     *      path="/territories",
     *      summary="Store a newly created Territory in storage",
     *      tags={"Territory"},
     *      description="Store Territory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Territory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Territory")
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
     *                  ref="#/definitions/Territory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTerritoryAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $territory = $this->territoryRepository->create($input);

        return $this->sendResponse($territory->toArray(), 'Territory saved successfully');
    }

    /**
     * @param int $id
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/territories/{id}",
     *      summary="Display the specified Territory",
     *      tags={"Territory"},
     *      description="Get Territory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Territory",
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
     *                  ref="#/definitions/Territory"
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
        /** @var Territory $territory */
        $territory = $this->territoryRepository->find($id);

        if ($territory === null) {
            return $this->sendError('Territory not found');
        }

        return $this->sendResponse($territory->toArray(), 'Territory retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTerritoryAPIRequest $request
     * @return JsonResponse
     *
     * @SWG\Put(
     *      path="/territories/{id}",
     *      summary="Update the specified Territory in storage",
     *      tags={"Territory"},
     *      description="Update Territory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Territory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Territory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Territory")
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
     *                  ref="#/definitions/Territory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTerritoryAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Territory $territory */
        $territory = $this->territoryRepository->find($id);

        if ($territory === null) {
            return $this->sendError('Territory not found');
        }

        $territory = $this->territoryRepository->update($input, $id);

        return $this->sendResponse($territory->toArray(), 'Territory updated successfully');
    }

    /**
     * @param int $id
     * @return JsonResponse
     *
     * @throws \Exception
     * @SWG\Delete(
     *      path="/territories/{id}",
     *      summary="Remove the specified Territory from storage",
     *      tags={"Territory"},
     *      description="Delete Territory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Territory",
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
    public function destroy($id): JsonResponse
    {
        /** @var Territory $territory */
        $territory = $this->territoryRepository->find($id);

        if ($territory === null) {
            return $this->sendError('Territory not found');
        }

        $territory->delete();

        return $this->sendResponse($id, 'Territory deleted successfully');
    }
}
