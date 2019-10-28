<?php


namespace App\Swagger\Models;

/**
 * @SWG\Definition(
 *      definition="CreateGroupRequest",
 *      required={"name,role"},
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="role",
 *          description="URL friendly Group slug",
 *          type="string"
 *      )
 * )
 */
class CreateGroupRequest
{

}