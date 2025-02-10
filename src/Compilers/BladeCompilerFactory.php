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

namespace Mimmi20\Mezzio\BladeRenderer\Compilers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\DynamicComponent;
use InvalidArgumentException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function array_key_exists;
use function is_array;
use function is_string;

final class BladeCompilerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     * @throws ServiceNotCreatedException
     */
    public function __invoke(ContainerInterface $container): BladeCompiler
    {
        $filesystem = $container->get(Filesystem::class);

        $config         = $container->has('config') ? $container->get('config') : [];
        $templateConfig = [];

        if (
            is_array($config)
            && array_key_exists('templates', $config)
            && is_array($config['templates'])
        ) {
            $templateConfig = $config['templates'];
        }

        $cachePath   = '';
        $shouldCache = false;

        if (
            array_key_exists('cache-path', $templateConfig)
            && is_string($templateConfig['cache-path'])
            && $templateConfig['cache-path'] !== ''
        ) {
            $cachePath   = $templateConfig['cache-path'];
            $shouldCache = true;
        }

        if ($cachePath === '') {
            throw new ServiceNotCreatedException('a cache path is required');
        }

        $blade = new BladeCompiler(
            files: $filesystem,
            cachePath: $cachePath,
            shouldCache: $shouldCache,
        );
        $blade->component(DynamicComponent::class, 'dynamic-component');

        return $blade;
    }
}
