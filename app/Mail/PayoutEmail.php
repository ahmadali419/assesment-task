<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayoutEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $amount;

    /**
     * Create a new message instance.
     *
     * @param  float  $amount
     * @return void
     */
    public function __construct(float $amount)
    {
        $this->amount = $amount;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.payout')
            ->subject('Payout Notification');
    }
}
