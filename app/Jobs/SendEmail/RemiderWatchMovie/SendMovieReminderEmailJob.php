<?php

namespace App\Jobs\SendEmail\RemiderWatchMovie;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMovieReminderEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;

    protected $subject;

    protected $view;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $view, $order)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->view = $view;
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::send($this->view, ['order' => $this->order], function ($message) {
                $message->to($this->email);
                $message->subject($this->subject);
            });
        } catch (\Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
        }
    }
}
