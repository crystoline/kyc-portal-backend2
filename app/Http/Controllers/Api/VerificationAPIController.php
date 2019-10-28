<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateVerificationAPIRequest;
use App\Http\Requests\API\UpdateVerificationAPIRequest;
use App\Models\Agent;
use App\Models\Verification;
use App\Repositories\AgentRepository;
use App\Repositories\VerificationRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use MongoDB\Driver\Query;
use Response;

/**
 * Class VerificationController
 * @package App\Http\Controllers\API
 */

class VerificationAPIController extends AppBaseController
{
    /** @var  VerificationRepository */
    private $verificationRepository;
    /**
     * @var AgentRepository
     */
    private $agentRepository;

    public function __construct(VerificationRepository $verificationRepo, AgentRepository $agentRepository)
    {
        $this->verificationRepository = $verificationRepo;
        $this->agentRepository = $agentRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/verifications",
     *      summary="Get a listing of the Verifications.",
     *      tags={"Agents Verification"},
     *      description="Get all Verifications",
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
     *                  @SWG\Items(ref="#/definitions/Verification")
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
        $verifications = $this->verificationRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($verifications->toArray(), 'Verifications retrieved successfully');
    }

    /**
     * @param CreateVerificationAPIRequest $request
     * @return JsonResponse
     *
     * @SWG\Post(
     *      path="/verifications",
     *      summary="Store a newly created Verification in storage",
     *      tags={"Agents Verification"},
     *      description="Store Verification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Verification that should be stored",
     *          @SWG\Schema(ref="#/definitions/CreateVerificationRequest")
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
     *                  ref="#/definitions/Verification"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateVerificationAPIRequest $request): JsonResponse
    {
        $input = $request->except(['personal_information', 'guarantor_information']);
        try{
            /** @var Agent $agent */
            $agent = $this->agentRepository->findOrCreate($input);
            if($agent !== null){
                $input['agent_id'] = $agent->id;
            }
            // check if verification is required
            $pending = $agent->verifications()->whereIn('status', [0, 2, 9])->first(); //pending verification
            if($pending){
                return $this->sendError('A verification data is still pending', 405);
            }
            //return $this->sendResponse($input, '');
            /** @var Verification $verification */
            $verification = $this->verificationRepository->create($input);
            //create personal information
            if($request->input('personal_information')) {
                $verification->personalInformation()->updateOrCreate($request->input('personal_information'));
            }
            //create guarantor information
            if($request->input('guarantor_information')){
                $verification->guarantorInformation()->updateOrCreate($request->input('guarantor_information'));
            }

            $verification->load(['personalInformation', 'guarantorInformation', 'verificationApprovals', 'documents']);
            return $this->sendResponse($verification->toArray(), 'Verification saved successfully');
        }catch (\Exception $exception){
            return $this->sendError('Could not create verification data'.$exception->getMessage(), 405);
        }

    }

    /**
     * @param int $id
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/verifications/{id}",
     *      summary="Display the specified Verification",
     *      tags={"Agents Verification"},
     *      description="Get Verification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Verification",
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
     *                  ref="#/definitions/Verification"
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
        /** @var Verification $verification */
        $verification = $this->verificationRepository->find($id);
        $verification->load([
            'personalInformation',
            'guarantorInformation',
            'verificationApprovals' => static function(Builder $builder) {
                $builder->latest();
            },

            'documents'
        ]);
        if ($verification === null) {
            return $this->sendError('Verification not found');
        }

        return $this->sendResponse($verification->toArray(), 'Verification retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateVerificationAPIRequest $request
     * @return JsonResponse
     *
     * @SWG\Put(
     *      path="/verifications/{id}",
     *      summary="Update the specified Verification in storage",
     *      tags={"Agents Verification"},
     *      description="Update Verification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Verification",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Verification that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Verification")
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
     *                  ref="#/definitions/Verification"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string",
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateVerificationAPIRequest $request): JsonResponse
    {
        $input = $request->except(['personal_information', 'guarantor_information']);

        /** @var Verification $verification */
        $verification = $this->verificationRepository->find($id);

        if ($verification === null) {
            return $this->sendError('Verification not found');
        }
        //TODO check if verification can be update
        if(!in_array($verification->status, [2, 0])){
            return $this->sendError('You can update verification data at this time');
        }

        $verification = $this->verificationRepository->update($input, $id);
        //create personal information
        if($request->input('personal_information')){
            $verification->personalInformation()->updateOrCreate($request->input('personal_information'));
        }
        //create guarantor information
        if($request->input('guarantor_information')){
            $verification->guarantorInformation()->updateOrCreate($request->input('guarantor_information'));
        }
        //TODO update documents information
        $verification->load(['personalInformation', 'guarantorInformation', 'verificationApprovals', 'documents']);

        return $this->sendResponse($verification->toArray(), 'Verification updated successfully');
    }

    /**
     * @todo
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function UploadDocuments($id, Request $request): JsonResponse
    {
        $verification = $this->verificationRepository->find($id);
        if ($verification === null) {
            return $this->sendError('Verification not found');
        }
    }
    public function UploadSingleDocument($id, Request $request): JsonResponse
    {
        $verification = $this->verificationRepository->find($id);
        if ($verification === null) {
            return $this->sendError('Verification not found');
        }
    }
    public function deleteDocument(){

    }
    public function verificationApproval(){

    }
}
