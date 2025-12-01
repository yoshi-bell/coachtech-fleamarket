<?php

namespace App\Mail;

use App\Models\Item;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public $item;
    public $rater;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Item $item, User $rater)
    {
        $this->item = $item;
        $this->rater = $rater;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('取引が完了しました')
            ->view('emails.transaction_completed');
    }
}
