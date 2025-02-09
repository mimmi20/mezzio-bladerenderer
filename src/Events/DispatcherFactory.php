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

namespace Mimmi20\Mezzio\BladeRenderer\Events;

use Illuminate\Events\Dispatcher;
use Mimmi20\Mezzio\BladeRenderer\Renderer\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class DispatcherFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Dispatcher
    {
        $rendererContainer = $container->get(Container::class);

        return new Dispatcher($rendererContainer);
    }
}
