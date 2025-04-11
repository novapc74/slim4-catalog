<?php

use Predis\Client;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

return function (ContainerInterface $container, array $settings): void {
    if ($settings['cache']['enabled']) {
        // Создаем клиент Redis
        $redisClient = new Client([
            'host' => $settings['cache']['host'],
            'port' => $settings['cache']['port'],
            'password' => $settings['cache']['password'] ?? null,
        ]);

        // Создаем адаптер Redis
        $redisAdapter = new RedisAdapter($redisClient);

        // Оборачиваем адаптер Redis в TagAwareAdapter
        $cache = new TagAwareAdapter($redisAdapter);

        // Регистрируем кэш в контейнере как TagAwareCacheInterface
        $container->set(TagAwareCacheInterface::class, $cache);
    }
};
