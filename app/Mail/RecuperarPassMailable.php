<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecuperarPassMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = "Este es el asunto de recuperar pass";
    private $password = "";
    private $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($password, $user)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pass = $this->password;
        $user = $this->user;
        return $this->view('recuperarPass', compact('pass', 'user'));
    }
}
