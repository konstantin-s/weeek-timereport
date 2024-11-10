<?php

namespace App\TimeReport;

use App\Common\Storage;
use App\Weeek\ApiClient;
use DateTime;
use Monolog\Logger;
use SplFileInfo;
use Throwable;

class Report
{
    protected Storage $storage;
    protected ApiClient $apiClient;
    protected Logger $l;

    public function __construct(Storage $storage, ApiClient $apiClient, Logger $logger)
    {
        $this->storage = $storage;
        $this->apiClient = $apiClient;
        $this->l = $logger->withName('TimeReport');
    }

    /**
     * @throws Throwable
     */
    public function buildReport(DateTime $dt): SplFileInfo
    {

        //Получить ВСЕ задачи по АПИ
        $tasks = $this->apiClient->getTasks();
        //@todo
        //Преобразовать данные:  группировка позадачно и суммированием в группах затраченного в запрошенном периоде времени
        //$taskTimeReportables = $tasks->groupWithTimesum($dt);
        //Сформировать файл таблицы эксель

        $nowStamp = date('Y-m-d_His');
        $reportFileDir = $this->storage->usedir("timereports_" . date('Y'));
        $reportFilename = "weeek_timereport_{$dt->format("Y-m")}@$nowStamp.json";
        $reportFilepath = $reportFileDir->getPathname() . DIRECTORY_SEPARATOR . $reportFilename;

        file_put_contents($reportFilepath, json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return new SplFileInfo($reportFilepath);
    }
}