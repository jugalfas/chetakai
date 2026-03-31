<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PlanChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $newPlan;
    public $oldPlan;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $newPlan, $oldPlan = null)
    {
        $this->user = $user;
        $this->newPlan = $newPlan;
        $this->oldPlan = $oldPlan;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Subscription Plan Updated - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.plan-changed',
            with: [
                'user' => $this->user,
                'newPlan' => $this->newPlan,
                'oldPlan' => $this->oldPlan,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
