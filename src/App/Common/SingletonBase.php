<?php

namespace App\Common;


use Exception;

/**
 * Если вам необходимо поддерживать в приложении несколько типов Одиночек, вы
 * можете определить основные функции Одиночки в базовом классе, тогда как
 * фактическую бизнес-логику (например, ведение журнала) перенести в подклассы.
 * https://refactoring.guru/ru/design-patterns/singleton/php/example#example-1
 */
class SingletonBase
{
    /**
     * Реальный экземпляр одиночки почти всегда находится внутри статического
     * поля. В этом случае статическое поле является массивом, где каждый
     * подкласс Одиночки хранит свой собственный экземпляр.
     */
    private static array $instances = [];

    /**
     * Конструктор Одиночки не должен быть публичным. Однако он не может быть
     * приватным, если мы хотим разрешить создание подклассов.
     */
    protected function __construct()
    {
    }

    /**
     * Клонирование и десериализация не разрешены для одиночек.
     */
    protected function __clone()
    {
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    /**
     * Метод, используемый для получения экземпляра Одиночки.
     * @return static
     */
    public static function i(): static
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            // Обратите внимание, что здесь мы используем ключевое слово
            // "static"  вместо фактического имени класса. В этом контексте
            // ключевое слово "static" означает «имя текущего класса». Эта
            // особенность важна, потому что, когда метод вызывается в
            // подклассе, мы хотим, чтобы экземпляр этого подкласса был создан
            // здесь.

            self::$instances[$subclass] = new static();
            self::$instances[$subclass]->init();
        }
        return self::$instances[$subclass];
    }

    protected function init()
    {
    }
}