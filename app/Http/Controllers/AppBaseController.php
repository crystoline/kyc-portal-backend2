<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Utils\ResponseUtil;

/**
 * @SWG\Swagger(
 *   @SWG\Info(
 *     title="KYC APIs",
 *     version="1.0.0",
 *   ),
 *      produces={"application/json", "application/xml"},
 *      @SWG\Parameter(
 *          type="string",
 *          name="Accept",
 *          in="header",
 *          default="application/json"
 *     )
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
class AppBaseController extends Controller
{
   use AppBaseTrait;
}
