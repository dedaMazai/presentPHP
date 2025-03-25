<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OutOfDiskSpace extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private string $freeSpace)
    {
    }

    public function build()
    {
        return $this->view('emails.out_of_disk_space', ['free_space' => $this->freeSpace]);
    }
}
