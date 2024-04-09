<?php

namespace App\Listeners;

use App\Events\ChirpCreated;
use App\Models\User;
use App\Notifications\NewChirp;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendChirpCreatedNotifications implements ShouldQueue
{
    /**
     * The name of the connection the job should be sent to.
     *
     * @var string|null
     */
    public $connection = 'sqs';

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'listeners';

    /**
     * The time (seconds) before the job should be processed.
     *
     * @var int
     */
    public $delay = 60;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ChirpCreated $event): void
    {
        foreach (User::whereNot('id', $event->chirp->user_id)->cursor() as $user) {
            $user->notify(new NewChirp($event->chirp));
        }
    }

    /**
     * Get the name of the listener's queue connection.
     */
    public function viaConnection(): string
    {
        return 'sqs';
    }

    /**
     * Get the name of the listener's queue.
     */
    public function viaQueue(): string
    {
        return 'listeners';
    }


}
