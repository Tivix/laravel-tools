<?php

namespace Kellton\Tools\Commands;

use Illuminate\Console\Command as BaseCommand;
use Kellton\Tools\Features\Dependency\Traits\UseDependency;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class Command handles the base logic of command.
 */
abstract class Command extends BaseCommand
{
    use UseDependency;

    /**
     * @var string The message format.
     */
    protected const MESSAGE_FORMAT = '%message%';

    /**
     * @var string The progress bar format.
     */
    protected const PROGRESS_BAR_FORMAT = '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%';

    /**
     * Create progress bar.
     *
     * @param int $total
     * @param string|null $message
     *
     * @return ProgressBar|null
     */
    protected function createProgressBar(int $total, ?string $message = null): ?ProgressBar
    {
        if (!($this->output->getOutput() instanceof ConsoleOutput)) {
            return null;
        }

        $progressBar = new ProgressBar($this->output->getOutput()->section(), $total);
        $progressBar->setFormat(self::MESSAGE_FORMAT . ' ' . self::PROGRESS_BAR_FORMAT);
        if ($message) {
            $progressBar->setMessage($message);
        }
        $progressBar->start();

        return $progressBar;
    }
}
