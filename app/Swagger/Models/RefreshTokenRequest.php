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
 *      definition="RefreshTokenRequest",
 *      required={""},
 *
 *      @SWG\Property(
 *          property="grant_type",
 *          description="Grant Type",
 *          type="string",
 *          default="refresh_token"
 *      ),
 *      @SWG\Property(
 *          property="client_id",
 *          description="Client ID",
 *          type="string",
 *          default="1"
 *      ),
 *       @SWG\Property(
 *          property="client_secret",
 *          description="Client Secret",
 *          type="string"
 *      ),
 *        @SWG\Property(
 *          property="refresh_token",
 *          description="Refresh Token",
 *          type="string"
 *      ),
 *        @SWG\Property(
 *          property="scope",
 *          description="All = *",
 *          type="string"
 *      )
 * )
 */
class RefreshTokenRequest
{

}