<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;


class BienvenueMentor extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Array $mentor, public string $password)
    {
    }
    public function envelope()
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: 'Bienvenue sur la plateforme de gestion des mentors : DOCOS',
        );
    }

    public function content()
{
    return new \Illuminate\Mail\Mailables\Content(
        view: 'mails.bienvenue-mentor-html', // Changement ici avec markdown
    );
}

}
