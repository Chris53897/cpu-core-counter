<?php

/*
 * This file is part of the Fidry CPUCounter Config package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fidry\CpuCounter;

use Fidry\CpuCounter\Exec\ExecException;
use Fidry\CpuCounter\Exec\ShellExec;
use function filter_var;
use function is_int;
use function trim;
use const FILTER_VALIDATE_INT;

/**
 * @see https://github.com/infection/infection/blob/fbd8c44/src/Resource/Processor/CpuCoresCountProvider.php#L69-L82
 * @see https://unix.stackexchange.com/questions/146051/number-of-processors-in-proc-cpuinfo
 */
final class NProcFinder
{
    private function __construct()
    {
    }

    /**
     * @return positive-int|null
     */
    public static function find(): ?int
    {
        if (!self::supportsNproc()) {
            return null;
        }

        try {
            $nproc = ShellExec::execute('nproc --all');
        } catch (ExecException $nprocFailed) {
            return null;
        }

        return self::countCpuCores($nproc);
    }

    private static function supportsNproc(): bool
    {
        try {
            $commandNproc = ShellExec::execute('command -v nproc');
        } catch (ExecException $noNprocCommand) {
            return false;
        }

        return '' !== trim($commandNproc);
    }

    /**
     * @return positive-int|null
     */
    public static function countCpuCores(string $nproc): ?int
    {
        $cpuCount = filter_var(trim($nproc), FILTER_VALIDATE_INT);

        return is_int($cpuCount) && $cpuCount > 0 ? $cpuCount : null;
    }
}
