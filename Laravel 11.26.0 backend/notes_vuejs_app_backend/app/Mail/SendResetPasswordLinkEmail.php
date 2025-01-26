<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendResetPasswordLinkEmail extends Mailable
{

    use Queueable, SerializesModels;

    public array $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $content)
    {
        //
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->content['subject'])
                    ->from("noreply@rollsvalley.com", config("app.name"))
                    ->replyTo("noreply@rollsvalley.com", "noreply")
                    ->view('emails/send_reset_password_email', [
                        "token" => $this->content['token']
                    ]);
    }
}
