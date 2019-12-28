<?php

namespace App\Mail;

use App\Models\VerificationPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationPeriodMail extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var VerificationPeriod
     */
    private $verificationPeriod;
    /**
     * @var VerificationPeriod
     */
    private $verification_period;

    /**
     * Create a new message instance.
     *
     * @param VerificationPeriod $verificationPeriod
     */
    public function __construct(VerificationPeriod $verificationPeriod)
    {
        //
        $this->verificationPeriod = $verificationPeriod;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $date = $this->verificationPeriod->date_start->format('Y-m-d');
        return $this->markdown('email.verification_period', ['verificationPeriod' => $this->verificationPeriod])
            ->subject("NEW VERIFICATION PERIOD STARTING ON: {$date}");
    }
}
