<?php

namespace App\Console\Commands;

use App\Jobs\SendEmail\RemiderWatchMovie\SendMovieReminderEmailJob;
use App\Services\Order\ReminderWatchMoviesService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendMovieReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $subject = 'Reminder: Your Movie is Starting Soon';

    protected $mailTemplate = 'emails.showtimeReminder.ShowtimeReminder';
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $listOrder = resolve(ReminderWatchMoviesService::class)->handle();

        if (isset($listOrder)) {
            foreach ($listOrder as $order) {
                $email = $order->user->email;
                dispatch(new SendMovieReminderEmailJob($email, $this->subject, $this->mailTemplate, $order));
            }
        }
    }
}
