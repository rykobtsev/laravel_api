<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $text;

    /**
     * Create a new message instance.
     */
    public function __construct($title, $text)
    {
        $this->title = $title;
        $this->text  = $text;
    }

    public function build()
    {
        return $this->view('emails.test')
            ->subject($this->title)
            ->with([
                'title' => $this->title,
                'text'  => $this->text,
            ]);
    }
}
