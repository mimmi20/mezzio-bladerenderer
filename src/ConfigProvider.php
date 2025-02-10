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
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\FileEngine;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\FileViewFinder;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Mimmi20\Mezzio\BladeRenderer\Compilers\BladeCompilerFactory;
use Mimmi20\Mezzio\BladeRenderer\Config\RepositoryFactory;
use Mimmi20\Mezzio\BladeRenderer\Engine\CompilerEngineFactory;
use Mimmi20\Mezzio\BladeRenderer\Engine\EngineResolverFactory;
use Mimmi20\Mezzio\BladeRenderer\Engine\FileEngineFactory;
use Mimmi20\Mezzio\BladeRenderer\Engine\LaminasEngine;
use Mimmi20\Mezzio\BladeRenderer\Engine\LaminasEngineFactory;
use Mimmi20\Mezzio\BladeRenderer\Engine\PhpEngineFactory;
use Mimmi20\Mezzio\BladeRenderer\Renderer\BladeRenderer;
use Mimmi20\Mezzio\BladeRenderer\Renderer\BladeRendererFactory;
use Mimmi20\Mezzio\BladeRenderer\Renderer\Container;
use Mimmi20\Mezzio\BladeRenderer\View\FileViewFinderFactory;

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
                Container::class => InvokableFactory::class,
                \Illuminate\Container\Container::class => InvokableFactory::class,
                LaminasEngine::class => LaminasEngineFactory::class,
                CompilerEngine::class => CompilerEngineFactory::class,
                FileEngine::class => FileEngineFactory::class,
                PhpEngine::class => PhpEngineFactory::class,
                EngineResolver::class => EngineResolverFactory::class,
                BladeCompiler::class => BladeCompilerFactory::class,
                FileViewFinder::class => FileViewFinderFactory::class,
                Repository::class => RepositoryFactory::class,
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
