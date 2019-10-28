<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="PersonalInformation",
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
 *          property="email",
 *          description="email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="phone_number",
 *          description="phone_number",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="phone_number2",
 *          description="phone_number2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="imei",
 *          description="imei",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bvn",
 *          description="bvn",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bank_account_name",
 *          description="bank_account_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bank_account_number",
 *          description="bank_account_number",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="product_of_interest",
 *          description="Money Transfer/Withdrawal Bill Payment Gaming ",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="designation",
 *          description="Principal Agent, Sole Agent",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="occupation",
 *          description="Profession/Occupation",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="home_address",
 *          description="home_address",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="outlet_address",
 *          description="outlet_address",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="outlet_type",
 *          description="Shop,Office,Kiosk,Umbrella,Mobile,Others ",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="landmark",
 *          description="landmark",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="lga_id",
 *          description="lga_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="state_id",
 *          description="state_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="latitude",
 *          description="latitude",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="name_of_acquirer",
 *          description="Name of Acquirer/TP",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="android_phone",
 *          description="0=No, 1=Yes",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="bluetooth_printer",
 *          description="0=No, 1=Yes",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="signature",
 *          description="signature",
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
class PersonalInformation extends Model
{
    // use SoftDeletes;

    public $table = 'personal_information';
    
//    const CREATED_AT = 'created_at';
//    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [

       

        'verification_id',
        'email',
        'phone_number',
        'phone_number2',
        'imei',
        'bvn',
        'bank_account_name',
        'bank_account_number',
        'product_of_interest',
        'designation',
        'occupation',
        'home_address',
        'outlet_address',
        'outlet_type',
        'landmark',
        'lga_id',
        'state_id',
        'latitude',
        'name_of_acquirer',
        'android_phone',
        'bluetooth_printer',
        'signature'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'verification_id' => 'integer',
        'email' => 'string',
        'phone_number' => 'string',
        'phone_number2' => 'string',
        'imei' => 'string',
        'bvn' => 'string',
        'bank_account_name' => 'string',
        'bank_account_number' => 'string',
        'product_of_interest' => 'string',
        'designation' => 'string',
        'occupation' => 'string',
        'home_address' => 'string',
        'outlet_address' => 'string',
        'outlet_type' => 'string',
        'landmark' => 'string',
        'lga_id' => 'integer',
        'state_id' => 'integer',
        'latitude' => 'string',
        'name_of_acquirer' => 'string',
        'android_phone' => 'boolean',
        'bluetooth_printer' => 'boolean',
        'signature' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        /** Agent Information  */
        'type' => 'required|in:principal-agent,sole-agent',
        'is_app_only' => 'required|in:0,1',
        'first_name' => 'required',
        'last_name' => 'required',
        'user_name' => 'required|unique:agents',
        'code' => 'required|unique:agents',
        'gender' => 'required|in:male,female',
        'date_of_birth' => 'required|date',
        /** End Agent Information */

        'verification_id' => 'required',
        'android_phone' => 'required',
        'bluetooth_printer' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function lga()
    {
        return $this->belongsTo(\App\Models\Lga::class, 'lga_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function state()
    {
        return $this->belongsTo(\App\Models\State::class, 'state_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function verification()
    {
        return $this->belongsTo(\App\Models\Verification::class, 'verification_id');
    }
}
