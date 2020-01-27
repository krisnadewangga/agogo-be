<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PusherEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
   
    public $message;
    public $userIDs;
    public $type;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($type,$message = null, $userIDs = null)
    {
        $this->message = $message;
        $this->userIDs = $userIDs;
        $this->type = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if($this->type == "1"){
            $channels = [];
            
            if ($this->userIDs == null) {
              $channels = ['agogo'];
            }else {
              foreach($this->userIDs as $userID) {
                array_push($channels, 'agogo.'.$userID);
              }
            }
        }else{
            $channels = ['agogoPesan'];
        }
        

        return $channels;
    }
}
