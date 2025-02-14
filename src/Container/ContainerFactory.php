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

namespace Mimmi20\Mezzio\BladeRenderer\Container;

use Illuminate\Config\Repository;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Illuminate\View\Component;
use Illuminate\View\ViewServiceProvider;
use Psr\Container\ContainerInterface;

final class ContainerFactory
{
    /** @throws void */
    public function __invoke(ContainerInterface $container): Container
    {
        $rendererContainer = new Container();

        \Illuminate\Container\Container::setInstance($rendererContainer);

        $rendererContainer->bindIf('files', static fn () => $container->get(Filesystem::class));
        $rendererContainer->bindIf('events', static fn () => $container->get(Dispatcher::class));
        $rendererContainer->bindIf('config', static fn () => $container->get(Repository::class));

        Facade::setFacadeApplication($rendererContainer);

        (new ViewServiceProvider($rendererContainer))->register();

        $rendererContainer->terminating(static function (): void {
            Component::flushCache();
        });

        return $rendererContainer;
    }
}
