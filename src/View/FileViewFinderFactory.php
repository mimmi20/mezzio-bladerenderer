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

namespace Mimmi20\Mezzio\BladeRenderer\View;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\FileViewFinder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function array_key_exists;
use function is_array;

final class FileViewFinderFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): FileViewFinder
    {
        $filesystem = $container->get(Filesystem::class);

        $config         = $container->has('config') ? $container->get('config') : [];
        $templateConfig = [];

        if (
            is_array($config) && array_key_exists('templates', $config) && is_array(
                $config['templates'],
            )
        ) {
            $templateConfig = $config['templates'];
        }

        $allPaths = isset($templateConfig['paths']) && is_array($templateConfig['paths'])
            ? $templateConfig['paths']
            : [];

        return new FileViewFinder(files: $filesystem, paths: $allPaths);
    }
}
