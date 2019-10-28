<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="GaurantorsInformation",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="verification_id",
 *          description="verification_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="full_name",
 *          description="full_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="occupation",
 *          description="Profession/Occupation",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="business_name",
 *          description="Business/Office Name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="office_address",
 *          description="office_address",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="position",
 *          description="Position/Status",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="department",
 *          description="Deparment or Unit",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="telephone_number",
 *          description="telephone_number",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="email",
 *          description="email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="residential_address",
 *          description="residential_address",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="mobile_number",
 *          description="mobile_number",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="relationship",
 *          description="Relationship to the Applicant:",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="no_of_relations_ship_years",
 *          description="Number of Years you have known the applicant?",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="signature",
 *          description="signature",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="witness_signature",
 *          description="witness_signature",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="witness_full_name",
 *          description="witness_full_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="witness_occupation",
 *          description="Profession/Occupation",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="witness__address",
 *          description="witness__address",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="witness_telephone_number",
 *          description="witness_telephone_number",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="witness_email",
 *          description="witness_email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class GuarantorsInformation extends Model
{
    // use SoftDeletes;

    public $table = 'guarantors_information';
    
    /*const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    */


    protected $dates = ['deleted_at'];


    public $fillable = [
        'verification_id',
        'full_name',
        'occupation',
        'business_name',
        'office_address',
        'position',
        'department',
        'telephone_number',
        'email',
        'residential_address',
        'mobile_number',
        'relationship',
        'no_of_relations_ship_years',
        'signature',
        'witness_signature',
        'witness_full_name',
        'witness_occupation',
        'witness__address',
        'witness_telephone_number',
        'witness_email'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'verification_id' => 'integer',
        'full_name' => 'string',
        'occupation' => 'string',
        'business_name' => 'string',
        'office_address' => 'string',
        'position' => 'string',
        'department' => 'string',
        'telephone_number' => 'string',
        'email' => 'string',
        'residential_address' => 'string',
        'mobile_number' => 'string',
        'relationship' => 'string',
        'no_of_relations_ship_years' => 'integer',
        'signature' => 'string',
        'witness_signature' => 'string',
        'witness_full_name' => 'string',
        'witness_occupation' => 'string',
        'witness__address' => 'string',
        'witness_telephone_number' => 'string',
        'witness_email' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'verification_id' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function verification()
    {
        return $this->belongsTo(\App\Models\Verification::class, 'verification_id');
    }
}
