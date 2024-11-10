<?php

namespace App\Common;

use Throwable;

class ExceptionHandler
{
    /**
     * Отвечает за обработку исключений, которые никто не поймал
     * @param Throwable $e
     * @return void
     * @example set_exception_handler([ExceptionHandler::class, "defaultHandler"]);
     */
    public static function defaultHandler(Throwable $e): void
    {
        $msg2log = self::prettify("Exception: {$e->getMessage()} @ {$e->getFile()}:{$e->getLine()}");
        error_log($msg2log);

        $msgs2show = [];
        $msgs2show[] = "При выполнении произошла ошибка";

        $toShow = $_ENV['EXCEPTION_SHOW_WHAT'] ?? '';

        switch ($toShow) {
            case 'message':
                $msgs2show[] = self::prettify($e->getMessage());
                break;

            case 'full':
                $msgs2show[] = self::prettify("{$e->getMessage()} @ {$e->getFile()}:{$e->getLine()}");
                $msgs2show[] = self::prettify($e->getTraceAsString(), true);
                break;
            default:
        }
        self::showAndDie($msgs2show);
    }

    public static function showAndDie(array $msgs2show): void
    {
        if (php_sapi_name() === 'cli') {
            self::showCLI($msgs2show);
        } else {
            http_response_code(500);
            self::showWeb($msgs2show);
        }
        exit(1);
    }

    public static function prettify(string $string, bool $moveVendor = false): string
    {
        $string = str_replace($_SERVER['DOCUMENT_ROOT'], '', $string);
        if ($moveVendor) {
            $string = str_replace("\\vendor\\", "\t\t\t\\vendor\\", $string);
        }
        return $string;
    }

    private static function showCLI(array $msgs2show): void
    {
        echo "\n" . str_repeat('-', 70) . "\n";
        echo join("\n\n", $msgs2show);
        echo "\n" . str_repeat('-', 70) . "\n";
    }

    private static function showWeb(array $msgs2show): void
    {
        echo "<hr>", nl2br(join("<br><br>", $msgs2show));
    }

}