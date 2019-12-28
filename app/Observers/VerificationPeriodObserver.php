<?php

namespace App\Observers;

use App\Mail\VerificationPeriodMail;
use App\Models\User;
use App\Models\VerificationPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class VerificationPeriodObserver
{
    /**
     * Handle the verification period "created" event.
     *
     * @param  \App\Models\VerificationPeriod  $verificationPeriod
     * @return void
     */
    public function created(VerificationPeriod $verificationPeriod): void
    {

        $users = User::query()->where(static function (Builder $builder) use ($verificationPeriod){
            if($verificationPeriod->territory_id !== null){
                $builder->orWhere('territory_id', $verificationPeriod->territory_id);
            }

            if(!empty($verificationPeriod->territory->state_id)) {
                $builder->orWhere(static function (Builder $builder) use ($verificationPeriod) {

                    $builder->whereHas('territory', static function (Builder $builder) use ($verificationPeriod) {
                        $builder->where('state_id', $verificationPeriod->territory->state_id);
                    });

                });
            }
            if(!empty( $verificationPeriod->lga->state_id)){
                $builder->orWhere(static function (Builder $builder) use($verificationPeriod){
                    $builder->whereHas('territory.lga', static function (Builder $builder) use ($verificationPeriod){
                        $builder->where('state_id', $verificationPeriod->lga->state_id);
                    });
                });
            }

        })->whereHas('group', static function (Builder $builder){
            $builder->where('name', setting('field_officer_role','field_officer'));
        })->get();
        Mail::to($users)
            ->send(new VerificationPeriodMail($verificationPeriod ));

    }

    /**
     * Handle the verification period "updated" event.
     *
     * @param  \App\Models\VerificationPeriod  $verificationPeriod
     * @return void
     */
    public function updated(VerificationPeriod $verificationPeriod)
    {
        //
    }

    /**
     * Handle the verification period "deleted" event.
     *
     * @param  \App\Models\VerificationPeriod  $verificationPeriod
     * @return void
     */
    public function deleted(VerificationPeriod $verificationPeriod)
    {
        //
    }

    /**
     * Handle the verification period "restored" event.
     *
     * @param  \App\Models\VerificationPeriod  $verificationPeriod
     * @return void
     */
    public function restored(VerificationPeriod $verificationPeriod)
    {
        //
    }

    /**
     * Handle the verification period "force deleted" event.
     *
     * @param  \App\Models\VerificationPeriod  $verificationPeriod
     * @return void
     */
    public function forceDeleted(VerificationPeriod $verificationPeriod)
    {
        //
    }
}
