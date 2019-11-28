<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDeviceOwnerAPIRequest;
use App\Http\Requests\API\UpdateDeviceOwnerAPIRequest;
use App\Models\DeviceOwner;
use App\Repositories\DeviceOwnerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class DeviceOwnerController
 * @package App\Http\Controllers\Api
 */

class DeviceOwnerAPIController extends AppBaseController
{
    /** @var  DeviceOwnerRepository */
    private $deviceOwnerRepository;

    public function __construct(DeviceOwnerRepository $deviceOwnerRepo)
    {
        $this->deviceOwnerRepository = $deviceOwnerRepo;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/device_owners,
     *      summary="Get a listing of the DeviceOwners.",
     *      tags={"DeviceOwner"},
     *      description="Get all DeviceOwners",
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
     *                  @SWG\Items(ref="#/definitions/DeviceOwner")
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
        $deviceOwners = $this->deviceOwnerRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($deviceOwners->toArray(), 'Device Owners retrieved successfully');
    }

    /**
     * @param CreateDeviceOwnerAPIRequest $request
     * @return JsonResponse
     *
     * @SWG\Post(
     *      path="/device_owners,
     *      summary="Store a newly created DeviceOwner in storage",
     *      tags={"DeviceOwner"},
     *      description="Store DeviceOwner",
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
     *          description="DeviceOwner that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DeviceOwner")
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
     *                  ref="#/definitions/DeviceOwner"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDeviceOwnerAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $deviceOwner = $this->deviceOwnerRepository->create($input);

        return $this->sendResponse($deviceOwner->toArray(), 'Device Owner saved successfully');
    }

    /**
     * @param int $id
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/deviceOwners/{id}",
     *      summary="Display the specified DeviceOwner",
     *      tags={"DeviceOwner"},
     *      description="Get DeviceOwner",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeviceOwner",
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
     *                  ref="#/definitions/DeviceOwner"
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
        /** @var DeviceOwner $deviceOwner */
        $deviceOwner = $this->deviceOwnerRepository->find($id);

        if ($deviceOwner === null) {
            return $this->sendError('Device Owner not found');
        }

        return $this->sendResponse($deviceOwner->toArray(), 'Device Owner retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDeviceOwnerAPIRequest $request
     * @return JsonResponse
     *
     * @SWG\Put(
     *      path="/deviceOwners/{id}",
     *      summary="Update the specified DeviceOwner in storage",
     *      tags={"DeviceOwner"},
     *      description="Update DeviceOwner",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeviceOwner",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DeviceOwner that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DeviceOwner")
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
     *                  ref="#/definitions/DeviceOwner"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDeviceOwnerAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var DeviceOwner $deviceOwner */
        $deviceOwner = $this->deviceOwnerRepository->find($id);

        if ($deviceOwner === null) {
            return $this->sendError('Device Owner not found');
        }

        $deviceOwner = $this->deviceOwnerRepository->update($input, $id);

        return $this->sendResponse($deviceOwner->toArray(), 'DeviceOwner updated successfully');
    }

    /**
     * @param int $id
     * @return JsonResponse
     *
     * @SWG\Delete(
     *      path="/deviceOwners/{id}",
     *      summary="Remove the specified DeviceOwner from storage",
     *      tags={"DeviceOwner"},
     *      description="Delete DeviceOwner",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeviceOwner",
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
        /** @var DeviceOwner $deviceOwner */
        $deviceOwner = $this->deviceOwnerRepository->find($id);

        if ($deviceOwner === null) {
            return $this->sendError('Device Owner not found');
        }

        $deviceOwner->delete();

        return $this->sendResponse($id, 'Device Owner deleted successfully');
    }
}
