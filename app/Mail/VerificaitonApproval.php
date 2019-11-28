<?php

namespace App\Mail;

use App\Models\Verification;
use App\Models\VerificationApproval;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificaitonApproval extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var VerificationApproval
     */
    private $approval;

    /**
     * Create a new message instance.
     *
     * @param VerificationApproval $approval
     */
    public function __construct(VerificationApproval $approval)
    {
        //
        $this->approval = $approval;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email.verification-approval', [
            'approval' => $this->approval
        ])->subject("VERIFICATION APPROVAL STATUS: {$this->approval->verification->first_name}, {$this->approval->status_text}");
    }
}
