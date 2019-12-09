<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\API\CreateAgentAPIRequest;
use App\Http\Requests\API\UpdateAgentAPIRequest;
use App\Imports\AgentsImport;
use App\Models\Agent;
use App\Repositories\AgentRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Maatwebsite\Excel\Validators\ValidationException;
use SebastianBergmann\Exporter\Exporter;
use Swagger\Annotations as SWG;

/**
 * Class AgentController
 * @package App\Http\Controllers\Api
 */

class AgentController extends AppBaseController
{
    /** @var  AgentRepository */
    private $agentRepository;

    public function __construct(AgentRepository $agentRepo)
    {
        $this->agentRepository = $agentRepo;
    }

    /**
     * @param string $input_key Request Key
     * @param string $upload_path Relative path
     * @param null $old
     * @param array $request_data
     * @return array
     * @throws Exception
     */
    public static function uploadBase64Image($input_key, $upload_path='',  $old = null, $request_data = array()): ?array
    {
        $base64_image = request()->input($input_key);
        if ($base64_image && $upload_path) {

            $upload_path =  trim($upload_path, '/\\');
            //$data = substr($base64_image, strpos($base64_image, ',') + 1);
            $data = base64_decode($base64_image);
            $image_name =  random_int(100000, 999999).time();
            $path = "public/{$upload_path}/" . $image_name;
            $image_link = asset("storage/{$upload_path}/" . $image_name);
            if(!Storage::exists("public/{$upload_path}")) {
                Storage::makeDirectory("public/{$upload_path}", 0775, true); //creates directory
            }

            if($old !== null ){ //remove old passport
                $public_pos =  strpos($old, 'storage/');
                if($public_pos !== false){
                    $old_image_path = 'public/'.substr($old, $public_pos+8);//include storage/ string
                    Storage::delete($old_image_path);
                }
            }
            //die($path);
            Storage::disk('local')->put($path, $data);
            if(empty($request_data)){
                request()->merge([(string)$input_key => $image_link]); //todo
            }else{
                $request_data =array_merge($request_data, [(string)$input_key => $image_link]);
            }

            return $request_data;
        }
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
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
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
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
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
        try {
            $base64_image = $request->input('passport');
            self::uploadBase64Image('passport', 'agents/passport');
            $input = $request->all();
            $agent = $this->agentRepository->create($input);
            return $this->sendResponse($agent->toArray(), 'Agent saved successfully');
        } catch (Exception $e) {
            die($e->getMessage().$e->getTraceAsString());
        }

        return $this->sendError('Unable to store agent record', 500);
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
     *          type="string",
     *          name="Authorization",
    *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="Agent's id or  code",
     *          type="string",
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
        $agent = Agent::query()->where('id', $id)->orWhere('code', $id)->first();// $this->agentRepository->find($id);

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
     *          type="string",
     *          name="Authorization",
    *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
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
        /** @var Agent $agent */
        $agent = $this->agentRepository->find($id);
        if ($agent === null) {
            return $this->sendError('Agent not found');
        }
        try{
            self::uploadBase64Image('passport', 'agents/passport', $agent->passport);

            $input = $request->except(['status']);
            $agent = $this->agentRepository->update($input, $id);

            return $this->sendResponse($agent->toArray(), 'Agent updated successfully');
        }catch (Exception $exception){

        }

        return $this->sendError('Agent record was not updated', 500);
    }

    /**
     * @param int $id
     * @return JsonResponse
     *
     * @SWG\Post(
     *      path="/agents/{id}/mark-as-created",
     *      summary="Mark agent as created",
     *      tags={"Agent"},
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
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function markAsCreated($id): JsonResponse
    {

        /** @var Agent $agent */
        $agent = $this->agentRepository->find($id);

        if ($agent === null) {
            return $this->sendError('Agent not found');
        }
        if($agent->status !== 2){
            return $this->sendError('Agent already created');
        }
        try{
            $agent = $this->agentRepository->update(['status' => 1], $id);
            return $this->sendResponse($agent,  'Agent status has been updated');
        }catch (\Exception $exception){
        }
        return $this->sendError('Could not change agent status');
        //$user->load('group');
    }

    /**
     * @SWG\Get(
     *      path="/agents/bulk-upload/download-template",
     *      summary="Download agent template",
     *      tags={"Agent"},
     *      description="",
     *      produces={"application/octet-stream"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful download"
     *      )
     * )
     */
    public function downloadUploadTemplate()
    {
        $lines = new Collection([
            [
                'AGENT CODE', 'PRINCIPAL AGENT CODE', 'FIRST NAME', 'LAST NAME',
                'GENDER', 'USER NAME', 'APP ONLY',  'PASSPORT URL',
                'DATE OF BIRTH', 'EMAIL', 'PHONE_NUMBER', 'ADDRESS', 'CITY', 'STATE', 'LGA'
            ],[
                'JOHNDOE', '', 'John', 'Doe',
                'Male', 'john.doe', 'Yes',  'http://url-to-passport-image-file',
                date('Y-m-d'), 'john.doe@mial.com', '08000000000', '1, Tinumbu Street', 'Lagos Island', 'Lagos', 'Lagos Island'
            ],[
                'EXAMPLE CODE 2', 'JOHNDOE', 'Mary', 'Doe',
                'Female', 'mary.doe', 'Yes',  'http://url-to-passport-image-file',
                date('Y-m-d'), 'mary.doe@mial.com', '08000000001', '2, Tinumbu Street', 'Lagos Island', 'Lagos', 'Lagos Island'
            ]


        ]);
        return $lines->downloadExcel(
            'agents-upload-template.xlsx'
        );
        //return Exporter::make('Excel')->load($lines)->stream('agents-upload-template');
    }


    /**
     * @SWG\Post(
     *      path="/agents/bulk-upload",
     *      summary="Upload agents",
     *      tags={"Agent"},
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
     *          name="upload",
     *          in="formData",
     *          required=true,
     *          description="Excel doc to be uploaded",
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
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @param Request $request
     * @return JsonResponse|null
     */
    public function uploadAgent(Request $request): ?JsonResponse
    {
        if($request->has('upload')){

            try {
                $file_path = $request->file('upload')->path();

                $output =  ExcelFacade::import(new AgentsImport(), $file_path, null,Excel::XLSX);
                    //->to(new AgentsImport(), $file_path,null, Excel::XLSX);

                return $this->sendResponse(null, 'Data was uploaded successfully');
            } catch (ValidationException $e) {
                $failures = $e->failures();
                $errors = new Collection();
                foreach ($failures as $failure) {

                    $errors->push([
                        'row' =>  $failure->row(),
                        'field' =>  $failure->attribute(),
                        'error' =>  $failure->errors(),
                        'value' =>  $failure->values(),
                    ]);
                }
                return $this->sendError($errors, 'Data can with some error');
            }
        }

        return $this->sendError('No data to upload', 422);
    }
}
