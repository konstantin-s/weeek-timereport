<?php

namespace App\TimeReport\Model;

class Task
{
    public int $id;
    public string $title;
    public int $projectId;
    public TaskTimeEntries $taskTimeEntries;

    public function __construct(int $id, string $title, int $projectId, TaskTimeEntries $taskTimeEntries)
    {
        $this->id = $id;
        $this->title = $title;
        $this->projectId = $projectId;
        $this->taskTimeEntries = $taskTimeEntries;
    }

    public static function fromWeeekJson(array $data): self
    {
        return new self((int)$data['id'], (string)$data['title'], (int)$data['projectId'], TaskTimeEntries::fromWeeekJson($data['timeEntries']));
    }
}