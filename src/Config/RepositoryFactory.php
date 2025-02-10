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

namespace Mimmi20\Mezzio\BladeRenderer\Config;

use Illuminate\Config\Repository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function array_key_exists;
use function is_array;
use function is_string;

final class RepositoryFactory
{
    /** @throws ContainerExceptionInterface */
    public function __invoke(ContainerInterface $container): Repository
    {
        $config         = $container->has('config') ? $container->get('config') : [];
        $templateConfig = [];

        if (
            is_array($config)
            && array_key_exists('templates', $config)
            && is_array($config['templates'])
        ) {
            $templateConfig = $config['templates'];
        }

        $cachePath = '';

        if (
            array_key_exists('cache-path', $templateConfig)
            && is_string($templateConfig['cache-path'])
            && $templateConfig['cache-path'] !== ''
        ) {
            $cachePath = $templateConfig['cache-path'];
        }

        $allPaths = isset($templateConfig['paths']) && is_array($templateConfig['paths'])
            ? $templateConfig['paths']
            : [];

        return new Repository([
            'view.paths' => $allPaths,
            'view.compiled' => $cachePath,
        ]);
    }
}
