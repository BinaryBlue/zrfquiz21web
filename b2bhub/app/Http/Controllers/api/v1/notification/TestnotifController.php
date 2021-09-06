<?php

namespace App\Http\Controllers\api\v1\notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BlueerpPusher implements ShouldBroadcast {
    public $message;
    public $otherdata;

    public function __construct($message)
    {
        $this->message = $message;
        $this->otherdata = 'Another Data';
    }
  
    public function broadcastOn()
    {
        return ['blueerp-channel'];
    }
  
    public function broadcastAs()
    {
        return 'blueerp-event';
    }
}

class TestnotifController extends Controller
{
    public function notiftest(){
        event(new BlueerpPusher('hello world'));
        return ['status'=>'OK'];
    }
}
