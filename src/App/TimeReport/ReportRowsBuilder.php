<?php

namespace App\TimeReport;

use App\TimeReport\Model\Projects;
use App\TimeReport\Model\Task;
use App\TimeReport\Model\Tasks;
use DateTime;

class ReportRowsBuilder
{

    protected Projects $projects;
    protected Tasks $tasks;

    public function __construct(Projects $projects, Tasks $tasks)
    {
        $this->projects = $projects;
        $this->tasks = $tasks;
    }

    /**
     * Группировка позадачно и суммированием в группах затраченного в запрошенном периоде времени, формирование массива строк с данными по задачам для отчета
     * @param DateTime $dt
     * @return array
     */
    public function buildReportRowsOfMonth(DateTime $dt): array
    {
        $targetYear = (int)$dt->format('Y');
        $targetMonth = (int)$dt->format('m');

        $projectId2Title = $this->projects->getMap();

        $taskMonthMinutes = [];
        foreach ($this->tasks->items as $task) {
            foreach ($task->taskTimeEntries->items as $taskTimeEntry) {
                if ($taskTimeEntry->y !== $targetYear || $taskTimeEntry->m !== $targetMonth) {
                    continue;
                }
                $taskMonthMinutes[$task->id] = ($taskMonthMinutes[$task->id] ?? 0) + $taskTimeEntry->duration;
            }
        }

        $taskIdsFitsForReport = array_keys($taskMonthMinutes);

        return collect($this->tasks->items)
            ->whereIn('id', $taskIdsFitsForReport)
            ->map(function (Task $task) use ($projectId2Title, $taskMonthMinutes) {
                return [
                    'project' => $projectId2Title[$task->projectId] ?? 'UNKNOWN',
                    'taskTitle' => $task->title,
                    'timeMinutes' => $taskMonthMinutes[$task->id],
                ];
            })
            ->sortByDesc('timeMinutes')
            ->toArray();

    }
}