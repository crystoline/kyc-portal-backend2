<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var User
     */
    private $user;
    /**
     * @var string
     */
    private $code;
    /**
     * @var string
     */
    private $return_url;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $code
     * @param string $return_url
     */
    public function __construct(User $user,string $code, string $return_url='')
    {
        //
        $this->user = $user;
        $this->code = $code;
        $this->return_url = $return_url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->markdown('email.password-reset', [
            'user'          => $this->user,
            'code'          => $this->code,
            'return_url'    => $this->return_url
        ])->subject('Password reset')->subject('Password reset successful');
    }
}
