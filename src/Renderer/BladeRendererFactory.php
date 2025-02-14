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

namespace Mimmi20\Mezzio\BladeRenderer\Renderer;

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Mimmi20\Mezzio\BladeRenderer\Container\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class BladeRendererFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ServiceNotCreatedException
     */
    public function __invoke(ContainerInterface $container): BladeRenderer
    {
        $rendererContainer = $container->get(Container::class);

        $factory  = $rendererContainer->get('view');
        $compiler = $rendererContainer->get('blade.compiler');
        $finder   = $rendererContainer->get('view.finder');

        return new BladeRenderer(factory: $factory, compiler: $compiler, finder: $finder);
    }
}
