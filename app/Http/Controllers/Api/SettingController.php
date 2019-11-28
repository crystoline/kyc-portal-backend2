<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Group;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Swagger\Annotations as SWG;


class SettingController extends AppBaseController
{


    //const MODEL = Setting::class;

    private static function getAllSettings(): array
    {
        $setting = Setting::settings();
        $s = [];
        foreach ($setting as $section){
            foreach ($section['elements'] as $element){
                $name = $element['name'];
                $value = setting($name);
                $s[$name]  =$value;
            }
        }
        return $s;
    }


    /**
     * @SWG\Get(
     *      path="/setting",
     *      summary="All setting data",
     *      tags={"Settings"},
     *      description="All setting data",
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
     *                  @SWG\Items(
     *                      @SWG\Property(
     *                          property="data",
     *                          type="string",
     *                          description="Data type"
     *                      ),
     *                      @SWG\Property(
     *                          property="name",
     *                          type="string",
     *                          description="Field name"
     *                      ),
     *                      @SWG\Property(
     *                          property="label",
     *                          type="string",
     *                          description="Field label"
     *                      ),
     *                      @SWG\Property(
     *                          property="rules",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="option",
     *                          type="array",
     *                           @SWG\Items(
     *                               @SWG\Property(
     *                                  property="value",
     *                                  type="string",
     *                                ),
     *                               @SWG\Property(
     *                                  property="name",
     *                                  type="string",
     *                                )
     *                           )
     *                      ),
     *                      @SWG\Property(
     *                          property="value",
     *                          type="string",
     *                          description="Default value"
     *                      )
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->sendResponse(Setting::settings(), '');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @SWG\Post(
     *      path="/setting",
     *      summary="Setting",
     *      tags={"Settings"},
     *      description="Store Setting",
     *      consumes={"application/json"},
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
     *          required=true,
     *          type="object",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Parameter(
     *                  name="re_verification_period",
     *                  type="integer"
     *              )
     *          )
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
     *                  @SWG\Parameter(
     *                      name="re_verification_period",
     *                      type="integer"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $rules = Setting::getValidationRules();
        $data = $this->validate($request, $rules);

        $validSettings = array_keys($rules);

        foreach ($data as $key => $val) {
            $path = '';
            if($request->hasFile($key) && $request->file($key)->isValid()){
                $original  =  setting($key);

                $path = $request->$key->store('public/settings'. $key);
                $val =  asset(str_replace('public/', 'storage/', $path));

                if($original && $original->path && file_exists(storage_path($original->path)) ){
                    Storage::delete($original->path);
                }

            }
            if ($val !== null && in_array($key, $validSettings, true)) {
                Setting::set($key, $val, Setting::getDataType($key), $path);
            }
        }

        $s  = self::getAllSettings();
        return $this->sendResponse($s, 'Setting Saved' );
    }


}
