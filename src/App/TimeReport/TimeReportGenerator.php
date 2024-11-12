<?php

namespace App\TimeReport;

use App\Common\Storage;
use App\TimeReport\Model\Projects;
use App\TimeReport\Model\Tasks;
use App\Weeek\ApiClient;
use DateTime;
use Monolog\Logger;
use SplFileInfo;
use Throwable;

class TimeReportGenerator
{
    protected Storage $storage;
    protected ApiClient $apiClient;
    protected Logger $l;
    protected ReportDataRenderer $excelGenerator;

    public function __construct(Storage $storage, ApiClient $apiClient, Logger $logger, ReportDataRenderer $excelGenerator)
    {
        $this->storage = $storage;
        $this->apiClient = $apiClient;
        $this->l = $logger->withName('TimeReport');
        $this->excelGenerator = $excelGenerator;
    }

    /**
     * @throws Throwable
     */
    public function buildReport(DateTime $dt): SplFileInfo
    {

        $dataProjects = $this->apiClient->getProjects();
        $projects = Projects::fromWeeekJson($dataProjects);

        $dataTasks = $this->apiClient->getTasks();
        $tasks = Tasks::fromWeeekJson($dataTasks);

        $reportRows = new ReportRowsBuilder($projects, $tasks);
        $reportRowsOfMonth = $reportRows->buildReportRowsOfMonth($dt);

        $nowStamp = date('Y-m-d_His');
        $reportFileDir = $this->storage->usedir("timereports_" . date('Y'));
        $reportFilename = "weeek_timereport_{$dt->format("Y-m")}@$nowStamp.csv";
        $reportFilepath = $reportFileDir->getPathname() . DIRECTORY_SEPARATOR . $reportFilename;

        return $this->excelGenerator->renderToFileCSV($dt, $reportRowsOfMonth, new SplFileInfo($reportFilepath));
    }
}