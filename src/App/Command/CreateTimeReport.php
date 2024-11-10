<?php

namespace App\Command;

use App\AppCore;
use App\Common\ExceptionHandler;
use App\TimeReport\Report;
use DateInterval;
use DateTime;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use Webmozart\Assert\Assert;

#[AsCommand(name: 'app:create-timereport', aliases: ['tr'])]
class CreateTimeReport extends Command
{

    protected function configure(): void
    {
        $this
            ->addArgument('month', InputArgument::OPTIONAL, 'Месяц: 1-12 или prev или cur', 'prev')
            ->addArgument('year', InputArgument::OPTIONAL, 'Год', date('Y'))
            ->setDescription('Генерирует отчет за указанный период: tr | tr prev | tr cur | tr 6 | tr 6 2020');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        try {
            $argMonth = $input->getArgument('month');
            $argYear = $input->getArgument('year');

            if ($argMonth === 'prev') {
                $reportDT = (new DateTime())->sub(new DateInterval('P1M'));
            } elseif ($argMonth === 'cur') {
                $reportDT = new DateTime();
            } else {
                $reportYear = (int)$argYear > 0 ? (int)$argYear : date('Y');
                Assert::range($reportYear, 2000, 3000, "Укажите корректный год");
                $reportMonth = (int)$argMonth >= 0 ? (int)$argMonth : date('n');
                Assert::range($reportMonth, 1, 12, "Укажите корректный месяц");
                $reportDT = DateTime::createFromFormat('Y-m', "$reportYear-$reportMonth");
            }
            $output->writeln("Запрошен отчёт за {$reportDT->format('Y')} год и {$reportDT->format('n')} месяц");
        } catch (Exception $e) {
            $output->writeln("Некорректная команда: {$e->getMessage()}");
            return Command::INVALID;
        }

        $report = AppCore::i()->get(Report::class);
        try {

            $reportFile = $report->buildReport($reportDT);

            $output->writeln(["Сгенерирован файл отчета:", $reportFile->getPathname()]);

            return Command::SUCCESS;

        } catch (Throwable $e) {
            ExceptionHandler::defaultHandler($e);
            return Command::FAILURE;
        }

    }

}