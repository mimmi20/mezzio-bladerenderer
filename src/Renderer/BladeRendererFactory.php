<?php

/**
 * This file is part of the mimmi20/blade-renderer package.
 *
 * Copyright (c) 2024-2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\Mezzio\BladeRenderer\Renderer;

use Mimmi20\Mezzio\BladeRenderer\Engine\LaminasEngine;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function array_key_exists;
use function is_array;
use function is_string;

final class BladeRendererFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): BladeRenderer
    {
        $rendererContainer = $container->get(Container::class);
        $laminasEngine     = $container->get(LaminasEngine::class);

        $config = $container->has('config') ? $container->get('config') : [];
        $config = is_array($config) && array_key_exists('templates', $config) && is_array($config['templates'])
            ? $config['templates']
            : [];

        $allPaths  = isset($config['paths']) && is_array($config['paths']) ? $config['paths'] : [];
        $cachePath = isset($config['cache-path']) && is_string($config['cache-path']) ? $config['cache-path'] : '';

        return new BladeRenderer(viewPaths: $allPaths, cachePath: $cachePath, laminasEngine: $laminasEngine, container: $rendererContainer);
    }
}
