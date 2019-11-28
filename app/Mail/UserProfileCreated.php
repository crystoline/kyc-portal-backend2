<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserProfileCreated extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var User
     */
    private $user;
    /**
     * @var string|null
     */
    private $host;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string|null $host
     */
    public function __construct(User $user, string $host= null)
    {
        //
        $this->user = $user;
        $this->host = $host;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email.user-profile-created',[
            'user' =>  $this->user,
            'host' =>  $this->host
        ])->subject('Profile information');
    }
}
