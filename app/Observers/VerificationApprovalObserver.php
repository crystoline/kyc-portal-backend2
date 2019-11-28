<?php

namespace App\Observers;

use App\Mail\PasswordResetSuccessful;
use App\Mail\VerificaitonApproval;
use App\Models\VerificationApproval;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class VerificationApprovalObserver
{
    /**
     * Handle the verification approval "created" event.
     *
     * @param VerificationApproval $verificationApproval
     * @return void
     */
    public function created(VerificationApproval $verificationApproval): void
    {
        $verificationApproval->verification->status = $verificationApproval->status;
        $verificationApproval->verification->save();

        //TODO update agent information.
        $agent = $verificationApproval->verification->agent;
       $new_agent_data = new Collection();

           //TODO delete previous passport
        $agent->passport = $verificationApproval->verification->passport?? $agent->passport;
        $agent->type =  $verificationApproval->verification->type?? $agent->type;
        $agent->parent_agent_id =  $verificationApproval->verification->parent_agent_id?? $agent->parent_agent_id;
        $agent->agent_type_id =  $verificationApproval->verification->agent_type_id?? $agent->agent_type_id;
        $agent->device_owner_id =  $verificationApproval->verification->device_owner_id?? $agent->device_owner_id;
        $agent->territory_id =  $verificationApproval->verification->territory_id?? $agent->territory_id;
        $agent->is_app_only =  $verificationApproval->verification->is_app_only?? $agent->is_app_only;
        $agent->first_name =  $verificationApproval->verification->first_name?? $agent->first_name;
        $agent->last_name =  $verificationApproval->verification->last_name?? $agent->last_name;
        $agent->user_name =  $verificationApproval->verification->user_name?? $agent->user_name;
        $agent->gender =  $verificationApproval->verification->gender?? $agent->gender;
        $agent->date_of_birth =  $verificationApproval->verification->date_of_birth?? $agent->date_of_birth;
        $agent->passport =  $verificationApproval->verification->passport?? $agent->passport;
        $agent->status =  $verificationApproval->verification->status?? $agent->status;
        $agent->address =  $verificationApproval->verification->home_address??$agent->address;
        $agent->phone_number =  $verificationApproval->verification->personalInformation->phone_number??$agent->phone_number;
        $agent->email =  $verificationApproval->verification->personalInformation->email??$agent->email;
        $agent->lga_id =  $verificationApproval->verification->personalInformation->lga_id??$agent->lga_id;
        $agent->state_id =  $verificationApproval->verification->personalInformation->state_id??$agent->state_id;
        $agent->save();

        //die( json_encode($verificationApproval->verification->verifiedBy));
        // Send Approval/Rejected Mail
        Mail::to($verificationApproval->verification->verifiedBy->email)
            ->send(new VerificaitonApproval($verificationApproval ));
    }

    /**
     * Handle the verification approval "updated" event.
     *
     * @param VerificationApproval $verificationApproval
     * @return void
     */
    public function updated(VerificationApproval $verificationApproval)
    {
        //
    }

    /**
     * Handle the verification approval "deleted" event.
     *
     * @param VerificationApproval $verificationApproval
     * @return void
     */
    public function deleted(VerificationApproval $verificationApproval)
    {
        //
    }

    /**
     * Handle the verification approval "restored" event.
     *
     * @param VerificationApproval $verificationApproval
     * @return void
     */
    public function restored(VerificationApproval $verificationApproval)
    {
        //
    }

    /**
     * Handle the verification approval "force deleted" event.
     *
     * @param VerificationApproval $verificationApproval
     * @return void
     */
    public function forceDeleted(VerificationApproval $verificationApproval)
    {
        //
    }
}
