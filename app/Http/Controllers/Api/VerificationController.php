<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateDocumentAPIRequest;
use App\Http\Requests\API\CreateVerificationAPIRequest;
use App\Http\Requests\API\CreateVerificationApprovalRequest;
use App\Http\Requests\API\UpdateVerificationAPIRequest;
use App\Models\Agent;
use App\Models\BvnVerification;
use App\Models\Document;
use App\Models\TelephoneVerification;
use App\Models\User;
use App\Models\Verification;
use App\Models\VerificationPeriod;
use App\Repositories\AgentRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\VerificationRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Response;

/**
 * Class VerificationController
 * @package App\Http\Controllers\Api
 */
class VerificationController extends AppBaseController
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
     * @param Verification $verification
     */
    private static function verificationLoadRelations(Verification $verification): void
    {
        $verification->load([
            'agent', 'parentAgent', 'verifiedBy', 'approvedBy',
            'deviceOwner', 'territory', 'verificationPeriod',
            'personalInformation',
            'guarantorInformation',
            'verificationApprovals' => static function (HasMany $hasMany) {
                return $hasMany->orderBy('created_at', 'DESC');
            },

            'documents'
        ])->append(['telephone_verification_status', 'bvn_verification_status', 'bvn_is_for_linked_agent']);
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
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
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
        )->load(['verifiedBy', 'approvedBy', 'agent']);

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
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
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
        try {
            DB::beginTransaction();
            /** @var Agent $agent */
            $agent = $this->agentRepository->findOrCreate($input);
            if ($agent !== null) {
                $input['agent_id'] = $agent->id;
            }
            // check if verification is required
            $pending = $agent->verifications()->whereIn('status', [0, 2, 9])->first(); //pending verification
            if ($pending) {
                return $this->sendError('A verification data is still pending', 405);
            }

            //Get verification periods
            /** @var VerificationPeriod $verificationPeriod */
            $verificationPeriod = VerificationPeriod::any($request, $agent)
                ->whereDoesntHave('verifications.agent', static function (Builder $builder) use($agent){
                    $builder->where('id', $agent->id);
                })
                ->multipleOrderBy([
                    'verification_periods.date_start' => 'DESC',
                    'territory_id' => 'DESC',
                'lga_id' => 'DESC',
                'state_id' => 'DESC',
            ])->first();

            if($verificationPeriod === null){
                return $this->sendError('Verification period not opened or completed', 405);
            }
            if(empty($input['verification_period_id'] )){
                $input['verification_period_id'] = $verificationPeriod->id;
            }


            /** @var User $user */
            $user = Auth::user();
            if ($user !== null) {
                $input['user_id'] = $user->id; //field officer id
            }

            AgentController::uploadBase64Image('passport', 'verifications/passport');

            /** @var Verification $verification */
            $verification = $this->verificationRepository->create($input);
            if($verification === null) {
                return $this->sendError('Could not create verification data', 405);
            }

            //create personal information
            if ($request->input('personal_information')) {
                AgentController::uploadBase64Image('personal_information.signature', 'verifications/signature');
                $verification->personalInformation()->create($request->input('personal_information'));
            }
            //create guarantor information
            if ($request->input('guarantor_information')) {
                AgentController::uploadBase64Image('guarantor_information.signature', 'guarantor_information/signature');
                AgentController::uploadBase64Image('guarantor_information.witness_signature', 'witness/signature');

                $verification->guarantorInformation()->create($request->input('guarantor_information'));
            }
            DB::commit();
            self::verificationLoadRelations($verification);
            // $verification->load(['agent', 'parentAgent', 'verifiedBy', 'approvedBy', 'personalInformation', 'guarantorInformation', 'verificationApprovals', 'documents']);
            return $this->sendResponse($verification->toArray(), 'Verification saved successfully');
        } catch (Exception $exception) {
            return $this->sendError('Could not create verification data' . $exception->getMessage(), 405);
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
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
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
        if ($verification === null) {
            return $this->sendError('Verification not found');
        }
        self::verificationLoadRelations($verification);


        return $this->sendResponse($verification->toArray(), 'Verification retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateVerificationAPIRequest $request
     * @return JsonResponse
     *
     * @throws Exception
     * @SWG\Put(
     *      path="/verifications/{id}",
     *      summary="Update the specified Verification in storage",
     *      tags={"Agents Verification"},
     *      description="Update Verification",
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
     *                  type="string",
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateVerificationAPIRequest $request): JsonResponse
    {

        /** @var Verification $verification */
        $verification = $this->verificationRepository->find($id);

        if ($verification === null) {
            return $this->sendError('Verification not found');
        }
        //check if verification can be update
        if (!in_array($verification->status, [2, 0])) {
            return $this->sendError('You can not update verification data at this time');
        }


        AgentController::uploadBase64Image('passport', 'verifications/passport', $verification->passport);

        $input = $request->except(['personal_information', 'guarantor_information']);
        $verification = $this->verificationRepository->update($input, $id);
        //create personal information
        if ($request->input('personal_information')) {
            AgentController::uploadBase64Image('personal_information.signature', 'verifications/signature', $verification->personalInformation->signature ?? null);
            $verification->personalInformation()->updateOrCreate($request->input('personal_information'));
        }
        //create guarantor information
        if ($request->input('guarantor_information')) {
            AgentController::uploadBase64Image('guarantor_information.signature', 'guarantor_information/signature', $verification->guarantorInformation->signature ?? null);
            AgentController::uploadBase64Image('guarantor_information.witness_signature', 'witness/signature', $verification->guarantorInformation->witness_signature ?? null);

            $verification->guarantorInformation()->updateOrCreate($request->input('guarantor_information'));
        }
        self::verificationLoadRelations($verification);
        return $this->sendResponse($verification->toArray(), 'Verification updated successfully');
    }

    /**
     * @param int $id
     * @return JsonResponse
     *
     * @SWG\Post(
     *      path="/verifications/{id}/publish",
     *      summary="Publish the specified Verification",
     *      tags={"Agents Verification"},
     *      description="Publish Verification",
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
    public function publish($id): JsonResponse
    {
        /** @var Verification $verification */
        $verification = $this->verificationRepository->find($id);
        if ($verification === null) {
            return $this->sendError('Verification not found');
        }

        if (!in_array($verification->status, [2, 0])) {
            return $this->sendError('You can not publish this verification data at this time', 403);
        }

        $verification->status = 9;
        $verification->save();

        self::verificationLoadRelations($verification);

        return $this->sendResponse($verification->toArray(), 'Verification data has been published successfully');
    }


    /**
     * @param $id
     * @param CreateDocumentAPIRequest $request
     * @param DocumentRepository $documentRepository
     * @return JsonResponse
     *
     * @SWG\Post(
     *      path="/verifications/{id}/upload-single-document",
     *      summary="Store a newly created Document in storage",
     *      tags={"Agents Verification"},
     *      description="Store Document",
     *      consumes={"multipart/form-data"},
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
     *          description="id of Verification",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="title",
     *          in="formData",
     *          required=true,
     *          description="Document title",
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="doc",
     *          in="formData",
     *          required=true,
     *          description="Document to be uploaded",
     *          type="file",
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
     *                  ref="#/definitions/Document"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function uploadSingleDocument($id, CreateDocumentAPIRequest $request, DocumentRepository $documentRepository): JsonResponse
    {
        /** @var Verification $verification */
        $verification = $this->verificationRepository->find($id);
        if ($verification === null) {
            return $this->sendError('Verification not found');
        }
        //check if verification can be update
        if (!in_array($verification->status, [2, 0])) {
            return $this->sendError('You can update verification data at this time');
        }

        if ($request->has('doc')) {
            $path = $request->file('doc')->store('public/documents');
            $request->merge(['path' => $path]);
        }
        $data = $request->only(['path', 'title']);
        $document = $verification->documents()->create($data);
        return $this->sendResponse($document->toArray(), 'Document saved successfully');
    }

    /**
     * @param DocumentRepository $documentRepository
     * @param $document_id
     * @return JsonResponse
     *
     * @throws Exception
     * @SWG\Post(
     *      path="/verifications/delete-single-document/{document_id}",
     *      summary="Delete a document",
     *      tags={"Agents Verification"},
     *      description="Delete Document",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *      ),
     *      @SWG\Parameter(
     *          name="document_id",
     *          description="id of Document",
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
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function deleteDocument(DocumentRepository $documentRepository, $document_id): JsonResponse
    {

        /** @var Document $document */
        $document = $documentRepository->find($document_id);

        if ($document === null) {
            return $this->sendError('Document not found');
        }
        //check if verification can be update
        if (!in_array($document->verification->status, [2, 0], true)) {
            return $this->sendError('You can not update verification data at this time');
        }
        //Storage::delete(directory);
        Storage::delete($document->path);
        $document->delete();

        return $this->sendResponse($document_id, 'Document deleted successfully');

    }

    /**
     * @SWG\Post(
     *      path="/verifications/{id}/approval",
     *      summary="Verification approval",
     *      tags={"Agents Verification"},
     *      description="Verification approval",
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
     *          description="id of Verification",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Verification that should be stored",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="status",
     *                  description="3=discard, 1=Approved, 0=Declined",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="comment",
     *                  description="comment",
     *                  type="string"
     *              ),
     *          ),
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
     *                  ref="#/definitions/VerificationApproval"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @param $id
     * @param CreateVerificationApprovalRequest $request
     * @return JsonResponse
     */
    public function verificationApproval($id, CreateVerificationApprovalRequest $request): JsonResponse
    {

        try {

            /** @var Verification $verification */
            $verification = $this->verificationRepository->find($id);
            if ($verification === null) {
                return $this->sendError('Verification not found');
            }
            if ($verification->status !== 9) {
                return $this->sendError('You can not update verification data at this time', 403);
            }
            /** @var User $user */
            $user = Auth::user();
            $data = $request->only(['status', 'comment']);
            if ($user !== null) {
                $data['user_id'] = $user->id;
            }
            DB::beginTransaction();
            $approval = $verification->verificationApprovals()->create($data);
            DB::commit();
            $approval->load(['user']);
            return $this->sendResponse($approval->toArray(), 'Verification approval update was successful');
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->sendResponse($exception->getMessage().$exception->getTraceAsString(), '');
        }

        return $this->sendError('Verification approval update was not successful', 403);
    }

    /**
     * @SWG\Post(
     *      path="/verifications/{id}/telephone/send-code",
     *      summary="Send SMS Verification Code",
     *      tags={"Agents Verification"},
     *      description="Verification approval",
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
     *                  property="data"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @param $verification_id
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyTelephone($verification_id, Request $request): JsonResponse
    {
        /*
        $this->validate($request, [
            'telephone' => 'required'
        ]);*/
        //$telephone = $request->input('telephone');
        try {

            /** @var Verification $verification */
            $verification = $this->verificationRepository->find($verification_id);
            if ($verification === null) {
                return $this->sendError('Verification data not found');
            }
            $telephone = $verification->personalInformation->phone_number ?? null;

            if ($telephone === null || empty($telephone)) {
                return $this->sendError('There no phone number to verify');
            }
            /** @var TelephoneVerification $phone_verification */
            $phone_verification = TelephoneVerification::query()->where([
                // 'agent_id' => $verification->agent_id,
                'telephone' => $telephone,
                // 'status' => 1
            ])->first();

            if ($phone_verification !== null) {
                if ($phone_verification->agent_id !== $verification->agent_id) {
                    return $this->sendError('Telephone Already registered by another agent', 403);
                }

                if ($phone_verification->status === 1) {
                    return $this->sendError('Telephone Already verified by this agent', 403);
                }
            }
            /** @var TelephoneVerification $telephone_verification */
            $telephone_verification = TelephoneVerification::query()->updateOrCreate(['agent_id' => $verification->agent_id, 'telephone' => $telephone], [
                'status' => 2,
                'code' => random_int(100000, 999999)
            ]);
            if ($telephone_verification) {
                //TODO Send Verification code to number
                //$telephone_verification
                $data = config('app.env') === 'local' ? $telephone_verification->only(['code']) : null;
                return $this->sendResponse($data, 'Verification code was sent');
            }

        } catch (Exception $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());
        }

        return $this->sendError('Could not send verification code', 500);
    }

    /**
     * @SWG\Post(
     *      path="/verifications/{id}/telephone/verify",
     *      summary="Confirm Verification code",
     *      tags={"Agents Verification"},
     *      description="Verification approval",
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
     *          description="id of Verification",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Verification data",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="code",
     *                  description="Verification Code",
     *                  type="string"
     *              ),
     *          ),
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
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @param $verification_id
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function verifyTelephoneConfirmation($verification_id, Request $request): JsonResponse
    {
        $this->validate($request, [
            'code' => 'required'
        ]);
        try {

            /** @var Verification $verification */
            $verification = $this->verificationRepository->find($verification_id);
            if ($verification === null) {
                return $this->sendError('Verification data not found');
            }
            $telephone = $verification->personalInformation->phone_number ?? null;

            if ($telephone === null || empty($telephone)) {
                return $this->sendError('There no phone number to verify');
            }

            /** @var TelephoneVerification $phone_verification */
            $phone_verification = TelephoneVerification::query()->where([
                // 'agent_id' => $verification->agent_id,
                'telephone' => $telephone,
                // 'status' => 1
            ])->first();

            if ($phone_verification !== null) {
                if ($phone_verification->agent_id !== $verification->agent_id) {
                    return $this->sendError('Telephone Already registered by another agent', 403);
                }

                if ($phone_verification->status === 1) {
                    return $this->sendError('Telephone Already verified by this agent', 403);
                }
            }
            if($phone_verification->code  === $request->input('code')){
                $phone_verification->status = 1;
                $phone_verification->save();
                return $this->sendResponse(null, 'Telephone number was successfully verified');
            }
            return $this->sendError('Verification does not match', 403);

        } catch (Exception $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());
        }

        return $this->sendError('Could complete telephone number verification', 500);
    }

    public function bvnData($verification_id): JsonResponse
    {

        try {
            /** @var Verification $verification */
            $verification = $this->verificationRepository->find($verification_id);
            if ($verification === null) {
                return $this->sendError('Verification data not found');
            }

            $bvn = $verification->personalInformation->bvn ?? null;

            if ($bvn === null || empty($bvn)) {
                return $this->sendError('There no bvn to verify');
            }
            /** @var TelephoneVerification $bvn_verification */
            $bvn_verification = BvnVerification::query()->where(['bvn' => $bvn])->first();

            if ($bvn_verification !== null) {
                if ($bvn_verification->agent_id !== $verification->agent_id && (!$verification->parent_agent_id || $bvn_verification->agent_id !== $verification->parent_agent_id)) {
                    return $this->sendError('BVN already registered by another agent', 403);
                }

                if ($bvn_verification->status === 1) {
                    return $this->sendError('BVN already verified by this agent', 403);
                }
                return $this->sendResponse($bvn_verification,'BVN data is available');
            }

            //Fetch BVN Data
            $bvn_data = [];
            if($bvn_data) {
                /** @var BvnVerification $telephone_verification */
                $bvn_verification = BvnVerification::query()->updateOrCreate([
                    'agent_id' => $verification->agent_id, 'bvn' => $bvn], [
                    'data' => $bvn_data
                ]);
                if ($bvn_verification) {
                    return $this->sendResponse($bvn_verification, 'BVN data fetched successfully');
                }
            }

        } catch (Exception $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());
        }

        return $this->sendError('Could not fetched BVN data', 500);
    }

    /**
     * @param $bvn_verification_id
     * @return JsonResponse
     */
    public function verifyBvn($bvn_verification_id): ?JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if($user === null){
            return $this->sendError('Unauthorized', 401);
        }

        /** @var BvnVerification $bvn_verification */
        $bvn_verification = BvnVerification::query()->find($bvn_verification_id);

        if ($bvn_verification !== null) {
            return $this->sendError('BVN Verification data not found');
        }

        if ($bvn_verification->status === 1) {
            return $this->sendError('BVN Verification data already verified', 403);
        }

        $bvn_verification->status = 1;
        $bvn_verification->user_id = $user->id;
        $bvn_verification->save();

        return $this->sendResponse($bvn_verification,'BVN data has been verified');

    }


}
