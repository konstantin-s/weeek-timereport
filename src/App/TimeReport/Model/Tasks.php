<?php

namespace App\TimeReport\Model;

class Tasks
{
    /** @var Task[] */
    public array $items;

    /**
     * @param Task[] $items
     */
    protected function __construct(array $items)
    {
        $this->items = $items;
    }

    public static function fromWeeekJson(array $dataTasks): self
    {
        $list = [];
        foreach ($dataTasks as $dataTask) {
            $list[] = Task::fromWeeekJson($dataTask);
        }
        return new self($list);
    }

}