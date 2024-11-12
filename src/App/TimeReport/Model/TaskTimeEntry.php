<?php

namespace App\TimeReport\Model;

class TaskTimeEntry
{
    public string $date;
    public int $duration;
    public int $y;
    public int $m;
    public int $d;

    protected function __construct(string $date, int $duration)
    {
        $this->date = $date;
        [$this->y, $this->m, $this->d] = array_map('intval', explode('-', $date));
        $this->duration = $duration;

    }

    public static function fromWeeekJson(array $data): self
    {
        return new self($data['date'], $data['duration']);
    }
}