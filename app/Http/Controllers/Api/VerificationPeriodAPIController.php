<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\API\CreateVerificationPeriodAPIRequest;
use App\Http\Requests\API\UpdateVerificationPeriodAPIRequest;
use App\Models\VerificationPeriod;
use App\Repositories\VerificationPeriodRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class VerificationPeriodController
 * @package App\Http\Controllers\API
 */

class VerificationPeriodAPIController extends AppBaseController
{
    /** @var  VerificationPeriodRepository */
    private $verificationPeriodRepository;

    public function __construct(VerificationPeriodRepository $verificationPeriodRepo)
    {
        $this->verificationPeriodRepository = $verificationPeriodRepo;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/verification_periods",
     *      summary="Get a listing of the VerificationPeriods.",
     *      tags={"VerificationPeriod"},
     *      description="Get all VerificationPeriods",
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
     *                  @SWG\Items(ref="#/definitions/VerificationPeriod")
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
        $verificationPeriods = $this->verificationPeriodRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($verificationPeriods->toArray(), 'Verification Periods retrieved successfully');
    }

    /**
     * @param CreateVerificationPeriodAPIRequest $request
     * @return JsonResponse
     *
     * @SWG\Post(
     *      path="/verification_periods",
     *      summary="Store a newly created VerificationPeriod in storage",
     *      tags={"VerificationPeriod"},
     *      description="Store VerificationPeriod",
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
     *          description="VerificationPeriod that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VerificationPeriod")
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
     *                  ref="#/definitions/VerificationPeriod"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateVerificationPeriodAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $verificationPeriod = $this->verificationPeriodRepository->create($input);

        return $this->sendResponse($verificationPeriod->toArray(), 'Verification Period saved successfully');
    }

    /**
     * @param int $id
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/verification_periods/{id}",
     *      summary="Display the specified VerificationPeriod",
     *      tags={"VerificationPeriod"},
     *      description="Get VerificationPeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VerificationPeriod",
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
     *                  ref="#/definitions/VerificationPeriod"
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
        /** @var VerificationPeriod $verificationPeriod */
        $verificationPeriod = $this->verificationPeriodRepository->find($id);

        if ($verificationPeriod === null) {
            return $this->sendError('Verification Period not found');
        }

        return $this->sendResponse($verificationPeriod->toArray(), 'Verification Period retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateVerificationPeriodAPIRequest $request
     * @return JsonResponse
     *
     * @SWG\Put(
     *      path="/verification_periods/{id}",
     *      summary="Update the specified VerificationPeriod in storage",
     *      tags={"VerificationPeriod"},
     *      description="Update VerificationPeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VerificationPeriod",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VerificationPeriod that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VerificationPeriod")
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
     *                  ref="#/definitions/VerificationPeriod"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateVerificationPeriodAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var VerificationPeriod $verificationPeriod */
        $verificationPeriod = $this->verificationPeriodRepository->find($id);

        if ($verificationPeriod === null) {
            return $this->sendError('Verification Period not found');
        }

        $verificationPeriod = $this->verificationPeriodRepository->update($input, $id);

        return $this->sendResponse($verificationPeriod->toArray(), 'VerificationPeriod updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/verification_periods/{id}",
     *      summary="Remove the specified VerificationPeriod from storage",
     *      tags={"VerificationPeriod"},
     *      description="Delete VerificationPeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VerificationPeriod",
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
        /** @var VerificationPeriod $verificationPeriod */
        $verificationPeriod = $this->verificationPeriodRepository->find($id);

        if (empty($verificationPeriod)) {
            return $this->sendError('Verification Period not found');
        }

        $verificationPeriod->delete();

        return $this->sendResponse($id, 'Verification Period deleted successfully');
    }
}
