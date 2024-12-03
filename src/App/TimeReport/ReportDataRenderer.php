<?php

namespace App\TimeReport;

use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

    public function renderToXLSX(DateTime $dt, array $reportRowsOfMonth, SplFileInfo $targetFile): SplFileInfo
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();


        $worksheet->setCellValue('A1', "Отчет за: {$dt->format("m Y")}");
        $worksheet->mergeCells('A1:F1');

        $worksheet->setCellValue('A2', 'Проект');
        $worksheet->setCellValue('B2', 'Задача');
        $worksheet->setCellValue('C2', 'Время');

        $worksheet->getColumnDimension('A')->setWidth(12);
        $worksheet->getColumnDimension('B')->setWidth(120);
        $worksheet->getColumnDimension('C')->setWidth(12);

        $styleArray = [
            'font' => ['bold' => true,],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_THIN,],
            ],
            'fill' => [
                'fillType' => Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => ['argb' => 'FFA0A0A0',],
                'endColor' => ['argb' => 'FFFFFFFF',],
            ],
        ];
        $worksheet->getStyle('A2:C2')->applyFromArray($styleArray);

        $rowNumDataStart = 3;
        $rowNumCur = $rowNumDataStart;
        foreach ($reportRowsOfMonth as $lineData) {
            $worksheet->setCellValue("A$rowNumCur", $lineData['project']);
            $worksheet->setCellValue("B$rowNumCur", $lineData['taskTitle']);
            $worksheet->setCellValue("C$rowNumCur", "={$lineData['timeMinutes']}/1440");
            $rowNumCur++;
        }
        $rowNumDataEnd = $rowNumCur - 1;

        $worksheet->setCellValue("B$rowNumCur", 'Итого:');
        $worksheet->getStyle("B$rowNumCur")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $worksheet->getStyle("B$rowNumCur")->getFont()->setBold(true);

        $worksheet->setCellValue("C$rowNumCur", "=SUM(C$rowNumDataStart:C$rowNumDataEnd)");
        $worksheet->getStyle("C$rowNumCur")->getFont()->setBold(true);

        $worksheet->getStyle("C$rowNumDataStart:C$rowNumCur")
            ->getNumberFormat()
            ->setFormatCode("[HH]:MM:SS");

        $writer = new Xlsx($spreadsheet);
        $writer->save($targetFile->getPathname());
        return new SplFileInfo($targetFile->getPathname());
    }
}