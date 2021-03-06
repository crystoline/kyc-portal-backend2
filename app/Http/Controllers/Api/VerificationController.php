<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateDocumentAPIRequest;
use App\Http\Requests\API\CreateVerificationAPIRequest;
use App\Http\Requests\API\CreateVerificationApprovalRequest;
use App\Http\Requests\API\UpdateVerificationAPIRequest;
use App\Models\Agent;
use App\Models\Bank;
use App\Models\BvnVerification;
use App\Models\Document;
use App\Models\TelephoneVerification;
use App\Models\User;
use App\Models\Verification;
use App\Models\VerificationPeriod;
use App\Repositories\AgentRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\VerificationRepository;
use App\Util\Bvn;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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
     * @param Request $request
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/api/v1/verifications",
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
     *      path="/api/v1/verifications",
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
                $request->merge(['agent_id'=> $agent->id]);
            }
            // check if verification is required
            $pending = $agent->verifications()->whereIn('status', [0, 2, 9])->first(); //pending verification
            if ($pending) {
                return $this->sendError('A verification data is still pending', 405);
            }

            //Get verification periods
            /** @var VerificationPeriod $verificationPeriod */
            $verificationPeriod = VerificationPeriod::any($request, $agent)
                ->whereDoesntHave('verifications.agent', static function (Builder $builder) use ($agent) {
                    $builder->where('id', $agent->id);
                })
                ->multipleOrderBy([
                    'verification_periods.date_start' => 'DESC',
                    'territory_id' => 'DESC',
                    'lga_id' => 'DESC',
                    'state_id' => 'DESC',
                ])->first();

            if ($verificationPeriod === null) {
                return $this->sendError('Verification period not opened or completed', 405);
            }
            if (!$request->input('verification_period_id')) {
                $request->merge(['verification_period_id' => $verificationPeriod->id]);
            }


            /** @var User $user */
            $user = Auth::user();
            if ($user !== null) {
                $request->merge(['user_id' => $user->id]); //field officer id
            }

            $input = $request->except(['personal_information', 'guarantor_information']);
            $input = AgentController::uploadBase64Image('passport', 'verifications/passport', null, $input);

            //die(json_encode($input));
            /** @var Verification $verification */
            $verification = $this->verificationRepository->create($input);
            if ($verification === null) {
                return $this->sendError('Could not create verification data', 405);
            }

            //create personal information
            if ($request->input('personal_information')) {
                $personal_information =  $request->input('personal_information');
                $personal_information = AgentController::uploadBase64Image('personal_information.signature', 'verifications/signature', null, $personal_information);
               //    die(json_encode($personal_information));
                $verification->personalInformation()->create($personal_information);
            }
            //create guarantor information
            if ($request->input('guarantor_information')) {
                $guarantor_information = $request->input('guarantor_information');
                $guarantor_information =  AgentController::uploadBase64Image('guarantor_information.signature', 'guarantor_information/signature', null, $guarantor_information);
                $guarantor_information = AgentController::uploadBase64Image('guarantor_information.witness_signature', 'witness/signature', null , $guarantor_information);

                $verification->guarantorInformation()->create($guarantor_information);
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
     * @param Verification $verification
     */
    private static function verificationLoadRelations(Verification $verification): void
    {
        $verification->load([
            'agent', 'parentAgent', 'verifiedBy', 'approvedBy',
            'deviceOwner', 'territory', 'verificationPeriod',
            'personalInformation.bank', 'personalInformation.lga',
            'guarantorInformation',
            'verificationApprovals' => static function (HasMany $hasMany) {
                return $hasMany->orderBy('created_at', 'DESC');
            },

            'documents' => static function (HasMany $hasMany) {
                $hasMany->orderBy('title', 'ASC');
                return $hasMany->orderBy('created_at', 'DESC');
            }
        ])->append(['telephone_verification_status', 'bvn_verification_status', 'bvn_is_for_linked_agent']);
    }

    /**
     * @param int $id
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/api/v1/verifications/{id}",
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
     *      path="/api/v1/verifications/{id}",
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

        $input = $request->except(['personal_information', 'guarantor_information', 'status']);
        $verification = $this->verificationRepository->update($input, $id);
        //create personal information
        //die(print_r($request->all(), 1));
        if ($request->input('personal_information')) {
            $personal_information = $request->input('personal_information');
            $personal_information = AgentController::uploadBase64Image('personal_information.signature', 'verifications/signature', $verification->personalInformation->signature ?? '', $personal_information);
            $verification->personalInformation()->updateOrCreate(['verification_id' => $id],$personal_information);
        }
        //create guarantor information
        if ($request->input('guarantor_information')) {
            $guarantor_information = $request->input('guarantor_information');
            $guarantor_information = AgentController::uploadBase64Image('guarantor_information.signature', 'guarantor_information/signature', $verification->guarantorInformation->signature ?? '', $guarantor_information);
            $guarantor_information = AgentController::uploadBase64Image('guarantor_information.witness_signature', 'witness/signature', $verification->guarantorInformation->witness_signature ?? '', $guarantor_information);

            $verification->guarantorInformation()->updateOrCreate(['verification_id' => $id], $guarantor_information);
        }
        self::verificationLoadRelations($verification);
        return $this->sendResponse($verification->toArray(), 'Verification updated successfully');
    }

    /**
     * @param int $id
     * @return JsonResponse
     *
     * @SWG\Post(
     *      path="/api/v1/verifications/{id}/publish",
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
     *      path="/api/v1/verifications/{id}/upload-single-document",
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
     *      path="/api/v1/verifications/delete-single-document/{document_id}",
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
     *      path="/api/v1/verifications/{id}/approval",
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
            return $this->sendResponse($exception->getMessage() . $exception->getTraceAsString(), '');
        }

        return $this->sendError('Verification approval update was not successful', 403);
    }

    /**
     * @SWG\Post(
     *      path="/api/v1/verifications/{id}/telephone/send-code",
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
                //send_sms_infobip('CAPRICON', '')
                $from = 'BAXI';//config('app.name');

                $sms_user_id = 'CapricornD';//setting('sms_user_id', 'CapricornD');
                $sms_password = 'P@$$w0rd2';//setting('sms_secret_key', 'P@$$w0rd2');
                $token = base64_encode($sms_user_id . ':' . $sms_password);
                // die($token);
                $response = send_sms_infobip($from, $telephone, "This is your verification code\n{$telephone_verification->code}", $token);
                //die($response);
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
     *      path="/api/v1/verifications/{id}/telephone/verify",
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
     * @param $id
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function verifyTelephoneConfirmation($id, Request $request): JsonResponse
    {
        $this->validate($request, [
            'code' => 'required'
        ]);
        try {

            /** @var Verification $verification */
            $verification = $this->verificationRepository->find($id);
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
            if ($phone_verification->code === $request->input('code')) {
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

    /**
     * @SWG\Post(
     *      path="/api/v1/verifications/{id}/bvn_data",
     *      summary="BVN Verification",
     *      tags={"Agents Verification"},
     *      description="BVN data",
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
     *                  ref="#/definitions/BvnVerification"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @param $id
     * @return JsonResponse
     */
    public function bvnData($id): JsonResponse
    {

        try {
            /** @var Verification $verification */
            $verification = $this->verificationRepository->find($id);
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
                //return $this->sendResponse($bvn_verification, 'BVN data is available');
            }

            // todo Fetch BVN Data
            $bvnClient = new Bvn();
            $bvn_data = $bvnClient->otherPartiesSingle($bvn);
            if ($bvn_data /*&& isset( $bvn_data['code']) && $bvn_data['code'] == '00'*/) {
                /** @var BvnVerification $telephone_verification */
                $bvn_verification = BvnVerification::query()->updateOrCreate([
                    'agent_id' => $verification->agent_id, 'bvn' => $bvn
                ], [
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
     * @SWG\Post(
     *      path="/api/v1/verifications/{bvn_verification_id}/verify_bvn",
     *      summary="BVN Verification approve",
     *      tags={"Agents Verification"},
     *      description="BVN data",
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
     *          description="id of BvnVerification",
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
     * @param $bvn_verification_id
     * @return JsonResponse
     */
    public function verifyBvn($bvn_verification_id): ?JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user === null) {
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

        return $this->sendResponse($bvn_verification, 'BVN data has been verified');

    }

    /**
     * @SWG\Post(
     *      path="/api/v1/verifications/{id}/account_name_enquiry",
     *      summary="Account name enquiry",
     *      tags={"Agents Verification"},
     *      description="Account name enquiry",
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
     *                  type="object",
     *                  @SWG\Property(
     *                      property="status",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="surname",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="othernames",
     *                      type="string"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @param $id
     * @return JsonResponse
     */
    public function nameEnquiry($id): ?JsonResponse
    {
        try {

            /** @var Verification $verification */
            $verification = $this->verificationRepository->find($id);
            if ($verification === null) {
                return $this->sendError('Verification data not found');
            }
            $bank = $verification->personalInformation->bank ?? null;
            ///$bank_account_name = $verification->personalInformation->bank_account_name??null;
            $bank_account_number = $verification->personalInformation->bank_account_number ?? null;
            if ($bank === null || $bank_account_number === null  /*|| $bank_account_name === null */) {
                return $this->sendError('Account information is not complete', 403);
            }
            $response = self::doNameEnquiry($bank, $bank_account_number);

            return $this->sendResponse($response, '');

        } catch (Exception $exception) {

        }
        return $this->sendError('Could not fetch data');
    }

    /**
     * @param Bank $bank
     * @param string $account_no
     * @return array
     * @throws Exception
     */
    public static function doNameEnquiry(Bank $bank, $account_no = ''): array
    {

        // $wsdl = 'http://css.ng/v1prod/name_enquiry';
        // $method = 'doNameEnquiry';
        $client_id = setting('css_name_enquiry_client_id', 'LHV5P67658');
        $secret_key = setting('css_name_enquiry_secret_key', '_u3_-HGR3gP3j97dazt35CHE96__GD');   //Secret key issued to the merchant by Upperlink
        $salt = random_int(100000, 1000000000);   //Unique salt to be generated by the client for each request (10 - 50 alphanumeric characters)
        $str2hash = "{$client_id}-{$secret_key}-{$salt}";
        $mac = hash('sha512', $str2hash);
        $data = [
            'enquiry_id' => '10001',
            'client_id' => $client_id,
            'bankcode' => $bank->nibss_code,
            'accno' => $account_no,
            'salt' => $salt,
            'mac' => $mac
        ];
        // print (\json_encode($data));

        $httpClient = new Client([
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $url = 'http://css.ng/v1prod/name_enquiry_rest';


        try {
            $response = $httpClient->request('POST', $url, [
                'body' => json_encode($data),
            ]);
            $response_code = $response->getStatusCode();

            if ($response_code == 200) {
                $content = $response->getBody()->getContents();
                return json_decode($content, true);
            }
            throw new Exception('Error ' . $response_code);

        } catch (GuzzleException $e) {

        } catch (Exception $e) {
            //die($e->getMessage());
            //return ('EXCEPTION: --' . $e->getLine() . ' -- ' . $e->getMessage());
        }
        return [];
    }


}
