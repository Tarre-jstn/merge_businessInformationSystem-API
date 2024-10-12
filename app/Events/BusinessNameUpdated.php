<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BusinessNameUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $oldName;
    public $newName;

    /**
     * Create a new event instance.
     *
     * @param string $oldName
     * @param string $newName
     */
    public function __construct($oldName, $newName)
    {
        $this->oldName = $oldName;
        $this->newName = $newName;
    }
}
