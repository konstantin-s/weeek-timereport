<?php

namespace App\Common;

use Exception;
use SplFileInfo;
use Webmozart\Assert\Assert;

/**
 * Отвечает за работу с хранилищем файлов приложения, используемых в его работе
 */
class Storage
{
    private SplFileInfo $storageDir;

    /**
     * @throws Exception
     */
    public function __construct(string $storageDir)
    {
        Assert::true(strlen($storageDir) && is_dir($storageDir), 'Не задана директория приложения');
        Assert::directory($storageDir, "Не удалось создать $storageDir");
        $this->storageDir = new SplFileInfo($storageDir);
    }

    /**
     * Создаёт в хранилище папку с указанным именем и возвращает путь к ней
     * @param string $dirname
     * @return SplFileInfo
     * @throws Exception
     */
    public function usedir(string $dirname): SplFileInfo
    {
        $subDir = $this->storageDir->getPathname() . DIRECTORY_SEPARATOR . $dirname;
        if (!is_dir($subDir)) {
            mkdir($subDir);
        }
        Assert::directory($subDir, "Не удалось создать $subDir");
        return new SplFileInfo($subDir);
    }
}