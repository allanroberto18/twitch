<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PusherEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var integer $userId
     */
    public $userId;

    /**
     * @var string $username
     */
    public $username;

    /**
     * @var string $title
     */
    public $title;

    /**
     * @var integer $viewers
     */
    public $viewers;

    /**
     * @var string $language
     */
    public $language;

    /**
     * @var string $message
     */
    public $message;

    /**
     * @var string $type
     */
    public $type;

    /**
     * PusherEvent constructor.
     * @param int $userId
     * @param string $username
     * @param string $title
     * @param int $viewers
     * @param string $language
     * @param string $type
     */
    public function __construct(
        int $userId,
        string $username,
        string $title,
        int $viewers,
        string $language,
        string $type,
        string $message = ''
    )
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->title = $title;
        $this->viewers = $viewers;
        $this->language = $language;
        $this->type = $type;

        $this->message = empty($message) ? $this->getMessage() : $message;
    }

    private function getMessage(): string
    {
        return sprintf('%s: %s, %d viewers - language: %s - %s',
            $this->username,
            $this->title,
            $this->viewers,
            $this->language,
            $this->type
        );
    }

    public function broadcastAs()
    {
        return 'event_changed';
    }

    /**
     * Get the channels the event should broadcast on.
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [ $this->userId ];
    }


}
