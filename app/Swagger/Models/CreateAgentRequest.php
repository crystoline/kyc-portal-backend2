<?php


namespace App\Swagger\Models;

/**
 * @SWG\Definition(
 *      definition="CreatAgentRequest",
 *      required={"type","is_app_only","first_name","last_name","user_name","gender","date_of_birth"},
 *      @SWG\Property(
 *          property="code",
 *          description="Agent's unique code",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="principal-agent, sole-agent",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="is_app_only",
 *          description="0=No,1=Yes",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="first_name",
 *          description="first_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="last_name",
 *          description="last_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="user_name",
 *          description="user_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="gender",
 *          description="Male, Female",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="date_of_birth",
 *          description="date_of_birth",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="passport",
 *          description="passport",
 *          type="string"
 *      )
 * )
 */
class CreateAgentRequest
{

}