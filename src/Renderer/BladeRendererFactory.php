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

use Illuminate\Events\Dispatcher;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Component;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\FileEngine;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Mimmi20\Mezzio\BladeRenderer\Engine\LaminasEngine;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

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
        $resolver          = $container->get(EngineResolver::class);

        $resolver->register(
            engine: 'file',
            resolver: static fn (): FileEngine => $container->get(FileEngine::class),
        );

        $resolver->register(
            engine: 'php',
            resolver: static fn (): PhpEngine => $container->get(PhpEngine::class),
        );

        $resolver->register(
            engine: 'blade',
            resolver: static fn (): CompilerEngine => $container->get(CompilerEngine::class),
        );

        $fileViewFinder = $container->get(FileViewFinder::class);

        $factory = new Factory(
            engines: $resolver,
            finder: $fileViewFinder,
            events: new Dispatcher($rendererContainer),
        );
        $factory->addExtension(
            extension: 'phtml',
            engine: 'LaminasEngine',
            resolver: static fn () => $laminasEngine,
        );

        $factory->setContainer($rendererContainer);

        $factory->share('app', $rendererContainer);

        $rendererContainer->terminating(static function (): void {
            Component::forgetFactory();
        });

        return new BladeRenderer(
            factory: $factory,
            compiler: $container->get(BladeCompiler::class),
            finder: $fileViewFinder,
        );
    }
}
