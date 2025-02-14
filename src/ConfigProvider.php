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

namespace Mimmi20\Mezzio\BladeRenderer;

use Illuminate\Config\Repository;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\FileViewFinder;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Mimmi20\Mezzio\BladeRenderer\Config\RepositoryFactory;
use Mimmi20\Mezzio\BladeRenderer\Container\Container;
use Mimmi20\Mezzio\BladeRenderer\Container\ContainerFactory;
use Mimmi20\Mezzio\BladeRenderer\Renderer\BladeRenderer;
use Mimmi20\Mezzio\BladeRenderer\Renderer\BladeRendererFactory;

final class ConfigProvider
{
    /**
     * @return array{dependencies: array{aliases: array<string, class-string>, factories: array<class-string|string, class-string>}, templates: array{cache-path: string, paths: array<int, string>}}
     *
     * @throws void
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
        ];
    }

    /**
     * @return array{aliases: array<string, class-string>, factories: array<class-string|string, class-string>}
     *
     * @throws void
     *
     * @api
     */
    public function getDependencies(): array
    {
        return [
            'aliases' => [
                'renderer.blade' => BladeRenderer::class,
                'view.engine.resolver' => EngineResolver::class,
                'view.finder' => FileViewFinder::class,
                'blade.compiler' => BladeCompiler::class,
            ],
            'factories' => [
                BladeRenderer::class => BladeRendererFactory::class,
                Container::class => ContainerFactory::class,
                \Illuminate\Container\Container::class => InvokableFactory::class,
                Repository::class => RepositoryFactory::class,
                Filesystem::class => InvokableFactory::class,
                Dispatcher::class => InvokableFactory::class,
            ],
        ];
    }

    /**
     * @return array{cache-path: string, paths: array<int, string>}
     *
     * @throws void
     *
     * @api
     */
    public function getTemplates(): array
    {
        return [
            'cache-path' => '',
            'paths' => [],
        ];
    }
}
