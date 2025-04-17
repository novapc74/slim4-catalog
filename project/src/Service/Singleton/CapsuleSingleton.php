<?php

namespace App\Service\Singleton;

use Exception;
use Illuminate\Database\Capsule\Manager as Capsule;

class CapsuleSingleton
{
    private static array $instances = [];

    /**
     * Конструктор Одиночки всегда должен быть скрытым, чтобы предотвратить
     * создание объекта через оператор new.
     */
    protected function __construct()
    {
    }

    /**
     * Одиночки не должны быть клонируемыми.
     */
    protected function __clone()
    {
    }

    /**
     * Одиночки не должны быть восстанавливаемыми из строк.
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Unable to deserialize singleton.");
    }

    public static function capsule(): Capsule
    {
        $class = static::class;

        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new Capsule();
        }

        return self::$instances[$class];
    }
}
