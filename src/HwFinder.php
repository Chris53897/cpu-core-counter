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

use function fgets;
use function is_resource;
use function pclose;
use function popen;

/**
 * Find the number of CPU cores for Linux, BSD and OSX.
 *
 * @see https://github.com/paratestphp/paratest/blob/c163539818fd96308ca8dc60f46088461e366ed4/src/Runners/PHPUnit/Options.php#L903-L909
 * @see https://opensource.apple.com/source/xnu/xnu-792.2.4/libkern/libkern/sysctl.h.auto.html
 */
final class HwFinder implements CpuCoreFinder
{
    private function __construct()
    {
    }

    /**
     * @return positive-int|null
     */
    public static function find(): ?int
    {
        $process = popen('sysctl -n hw.ncpu', 'rb');

        if (is_resource($process)) {
            // *nix (Linux, BSD and Mac)
            $cores = (int) fgets($process);
            pclose($process);

            return $cores;
        }

        return null;
    }
}