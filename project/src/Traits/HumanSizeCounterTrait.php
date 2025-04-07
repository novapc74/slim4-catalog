<?php

namespace App\Traits;

trait HumanSizeCounterTrait
{
    private function getHumanSize(string $data): string
    {
        $size = strlen($data);
        $units = ['B', 'kB', 'MB', 'GB'];

        foreach ($units as $unit) {
            if ($size < 1024) {
                break;
            }
            $size /= 1024;
        }

        return round($size, 2) . $unit;
    }

    private function formatTime(float $seconds): string
    {
        $seconds = ceil($seconds);

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        $timeString = '';

        if ($hours > 0) {
            $timeString .= $hours . 'ч ';
        }

        if ($minutes > 0) {
            $timeString .= $minutes . 'мин ';
        }

        if ($seconds > 0) {
            $timeString .= $seconds . 'сек';
        }

        return trim($timeString);
    }

    private function getExecutionTime(float $startScriptTime): string
    {
        return $this->formatTime($startScriptTime + microtime(true));
    }

    private function getScriptStartTime(): float
    {
        return -microtime(true);
    }

    public function humanizeUsageMemory(bool $realUsage = false): string
    {
        $memoryUsage = memory_get_usage($realUsage);

        return match (true) {
            $memoryUsage < 1024 => "{$memoryUsage} bytes",
            $memoryUsage < 1048576 => round($memoryUsage / 1024) . " KB",
            default => round($memoryUsage / 1048576, 2) . " MB",
        };
    }
}
