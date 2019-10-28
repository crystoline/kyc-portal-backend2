<?php

namespace App\Observers;

use App\Models\VerificationApproval;

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
        //TODO Send Approval/Rejected Mail
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
