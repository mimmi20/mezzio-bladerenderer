<?php

/**
 * This file is part of the mimmi20/mezzio-bladerenderer package.
 *
 * Copyright (c) 2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\Mezzio\BladeRenderer\Filesystem;

use Illuminate\Filesystem\Filesystem;
use Psr\Container\ContainerInterface;

final class FilesystemFactory
{
    /**
     * @throws void
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function __invoke(ContainerInterface $container): Filesystem
    {
        return new Filesystem();
    }
}
