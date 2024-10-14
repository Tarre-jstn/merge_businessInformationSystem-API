<?php

namespace App\Events;

class BusinessNameUpdated
{
    public $oldName;
    public $newName;
    public $changes;
    public $oldImage;

    /**
     * Create a new event instance.
     *
     * @param string $oldName
     * @param string $newName
     * @param array $changes
     * @param string|null $oldImage
     */
    public function __construct($oldName, $newName, $changes, $oldImage = null)
    {
        $this->oldName = $oldName;
        $this->newName = $newName;
        $this->changes = $changes;
        $this->oldImage = $oldImage;
    }
}
