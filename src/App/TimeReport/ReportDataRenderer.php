<?php

namespace App\TimeReport;

use DateTime;
use SplFileInfo;

class ReportDataRenderer
{

    /**
     * Генерирует файл excel с отчётом по времени за указанный месяц позадачно
     * @param DateTime $dt
     * @param array $reportRowsOfMonth
     * @param SplFileInfo $targetFile
     * @return SplFileInfo
     */
    public function renderToFileCSV(DateTime $dt, array $reportRowsOfMonth, SplFileInfo $targetFile): SplFileInfo
    {

        //@todo сделать xlsx с ЧЧ:ММ и суммированием и т.д.
        $fileCsv = fopen($targetFile->getPathname(), 'w');
        fprintf($fileCsv, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($fileCsv, ['Отчет за:', $dt->format("m Y"), ''], ';');
        fputcsv($fileCsv, ['Проект', 'Задача', 'Минут'], ';');
        foreach ($reportRowsOfMonth as $lineData) {
            fputcsv($fileCsv, array_values($lineData), ';');
        }
        fclose($fileCsv);

        return new SplFileInfo($targetFile->getPathname());
    }
}