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
    public $changes; // Changes array

    /**
     * Create a new event instance.
     *
     * @param string $oldName
     * @param string $newName
     * @param array $changes
     */
    public function __construct($oldName, $newName, $changes = [])
    {
        $this->oldName = $oldName;
        $this->newName = $newName;
        $this->changes = $changes;
    }
}
