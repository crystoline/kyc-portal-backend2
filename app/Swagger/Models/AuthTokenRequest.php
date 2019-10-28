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
 *      definition="AuthTokenRequest",
 *      required={""},
 *
 *      @SWG\Property(
 *          property="grant_type",
 *          description="Grant Type",
 *          type="string",
 *          default="password"
 *      ),
 *      @SWG\Property(
 *          property="client_id",
 *          description="Client ID",
 *          type="string"
 *      ),
 *       @SWG\Property(
 *          property="client_secret",
 *          description="Client Secret",
 *          type="string"
 *      ),
 *        @SWG\Property(
 *          property="username",
 *          description="Username",
 *          type="string"
 *      ),
 *        @SWG\Property(
 *          property="password",
 *          description="User password",
 *          type="string"
 *      ),
 *        @SWG\Property(
 *          property="scope",
 *          description="All = *",
 *          type="string"
 *      )
 * )
 */
class AuthTokenRequest
{

}