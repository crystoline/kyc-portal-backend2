<?php
/**
 * Created by PhpStorm.
 * User: crysto
 * Date: 19/10/22
 * Time: 12:35 PM
 */

namespace App\Swagger\Models;


/**
 * @SWG\Definition(
 *      definition="AuthTokenResponse",
 *      required={""},
 *
 *      @SWG\Property(
 *          property="token_type",
 *          description="Token Type",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="expires_in",
 *          description="Expiry",
 *          type="integer",
 *          format="int32"
 *      ),
 *       @SWG\Property(
 *          property="access_token",
 *          description="Client access token",
 *          type="string"
 *      ),
 *        @SWG\Property(
 *          property="refresh_token",
 *          description="Refresh Token",
 *          type="string"
 *      )
 * )
 */
class AuthTokenResponse
{

}