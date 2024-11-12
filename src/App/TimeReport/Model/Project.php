<?php

namespace App\TimeReport\Model;

class Project
{
    public int $id;
    public string $title;

    public function __construct(int $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    public static function fromWeeekJson(array $data): self
    {
        return new self((int)$data['id'], (string)$data['title']);
    }
}