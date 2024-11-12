<?php

namespace App\TimeReport\Model;

class Projects
{
    /** @var Project[] */
    public array $items;

    /**
     * @param Project[] $items
     */
    protected function __construct(array $items)
    {
        $this->items = $items;
    }

    public static function fromWeeekJson(array $dataProjects): self
    {
        $list = [];
        foreach ($dataProjects as $dataProject) {
            $list[] = Project::fromWeeekJson($dataProject);
        }
        return new self($list);
    }

    public function getMap(): array
    {
        return collect($this->items)->mapWithKeys(function (Project $item) {
            return [$item->id => $item->title];
        })->toArray();
    }
}