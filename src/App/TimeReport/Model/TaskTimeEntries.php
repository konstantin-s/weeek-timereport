<?php

namespace App\TimeReport\Model;

class TaskTimeEntries
{
    /** @var TaskTimeEntry[] */
    public array $items;

    /**
     * @param TaskTimeEntry[] $items
     */
    protected function __construct(array $items)
    {
        $this->items = $items;
    }

    public static function fromWeeekJson(array $timeEntries): self
    {
        $list = [];
        foreach ($timeEntries as $timeEntry) {
            $list[] = TaskTimeEntry::fromWeeekJson($timeEntry);
        }
        return new self($list);
    }
}