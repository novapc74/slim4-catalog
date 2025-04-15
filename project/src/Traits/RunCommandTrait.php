<?php

namespace App\Traits;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Style\OutputStyle;
use Throwable;

trait RunCommandTrait
{
    /**
     * @throws Throwable
     */
    public function runCommand(string $message, string $commandName, OutputStyle $io, array $definition = null): void
    {
//        $io->note($message);

        $arrayInputArgument = ['command' => $commandName];
        if ($definition) {
            $arrayInputArgument = array_merge($arrayInputArgument, $definition);
        }

        $greetInput = new ArrayInput($arrayInputArgument);

        $this->getApplication()->doRun($greetInput, $io);
    }
}
