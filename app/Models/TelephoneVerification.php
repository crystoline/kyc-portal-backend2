<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer status
 * @property integer agent_id
 * @property string code
 */
class TelephoneVerification extends Model
{
    protected $appends = [ 'stauts_text'];
    protected $fillable = [
        'agent_id',
        'telephone',
        'code',
        'status',
    ];
    protected $casts = [
        'status' => 'integer'
    ];

    /**
     * @return string
     */
    public function getStatusTextAttribute(): string
    {
        if( $this->status === 1) {
            return 'Verified';
        }
        return 'Not -Verified';
    }
}
