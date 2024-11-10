<?php

namespace App;

use App\Common\ExceptionHandler;
use App\Common\SingletonBase;
use App\Common\Storage;
use DI\Container;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use function DI\factory;

/**
 * Ядро приложения с контейнером php-di
 */
class AppCore extends SingletonBase
{
    /** @var Container */
    private Container $container;

    protected function init(): void
    {
        if (empty($_SERVER["DOCUMENT_ROOT"])) {
            $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__DIR__, 2));
        }

        $dotenv = Dotenv::createImmutable($_SERVER["DOCUMENT_ROOT"]);
        $dotenv->load();

        set_exception_handler([ExceptionHandler::class, "defaultHandler"]);

        try {

            $containerBuilder = new ContainerBuilder();

            $definitions = [
                'storageDir' => $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "storage",
                LoggerInterface::class => factory(
                    function (ContainerInterface $c) {

                        $logFilename = php_sapi_name() === 'cli' ? "cli" . DIRECTORY_SEPARATOR . "app-cli.log" : "app.log";
                        $logFilePath = $c->get('storageDir') . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $logFilename;

                        $rotatingFileHandler = new RotatingFileHandler($logFilePath, 180, Level::Debug);
                        $rotatingFileHandler->setFormatter(new LineFormatter(null, "Y-m-d H:i:s"));

                        $alwaysLogLever = $_ENV['APP_LOG_LEVEL_ALWAYS'] ?: Level::Warning;
                        $fingersCrossedHandler = new FingersCrossedHandler($rotatingFileHandler, Level::Error, 0, true, true, $alwaysLogLever);

                        return new Logger('basic', [$fingersCrossedHandler], [new UidProcessor(3)]);
                    }
                ),
                Logger::class => factory(fn(ContainerInterface $c) => $c->get(LoggerInterface::class)),
                Storage::class => factory(fn(ContainerInterface $c) => new Storage($c->get('storageDir'))),
            ];

            $containerBuilder->addDefinitions($definitions);

            $this->container = $containerBuilder->build();

            $this->logInit();
        } catch (Throwable $e) {
            ExceptionHandler::defaultHandler($e);
        }
    }


    /**
     * Получение записи из контейнера php-di
     */
    public function get(string $name)
    {
        try {
            return $this->container->get($name);
        } catch (Throwable $e) {
            $this->get(Logger::class)->critical(ExceptionHandler::prettify("Exception: {$e->getMessage()} @ {$e->getFile()}:{$e->getLine()}"));
            ExceptionHandler::defaultHandler($e);
            exit;
        }
    }

    private function logInit(): void
    {
        $isXHR = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        $reqTypeMark = $isXHR ? ' XHR' : '';
        $this->get(Logger::class)->debug("init$reqTypeMark", [$_SERVER['REQUEST_URI'] ?? $_SERVER['argv']]);
    }

}