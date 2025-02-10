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

use Illuminate\Container\Container;
use Jenssegers\Blade\Blade;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
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
     * @throws ServiceNotCreatedException
     */
    public function __invoke(ContainerInterface $container): BladeRenderer
    {
//        $rendererContainer = $container->get(Container::class);
//        $laminasEngine     = $container->get(LaminasEngine::class);
//
//        $rendererContainer->bindIf('files', function () {
//            return new Filesystem();
//        });
//        $rendererContainer->bindIf('events', function () use ($rendererContainer) {
//            return new Dispatcher($rendererContainer);
//        });
//        $rendererContainer->bindIf('config', function () use ($container) {
//            return $container->get(Repository::class);
//        });
//
//        Facade::setFacadeApplication($rendererContainer);
//
//        $rendererContainer->singleton('view', function ($app) use ($laminasEngine): Factory {
//            // Next we need to grab the engine resolver instance that will be used by the
//            // environment. The resolver will be used by an environment to get each of
//            // the various engine implementations such as plain PHP or Blade engine.
//            $resolver = $app['view.engine.resolver'];
//
//            $finder = $app['view.finder'];
//
//            $factory = new Factory($resolver, $finder, $app['events']);
//
//            // We will also set the container instance on this view environment since the
//            // view composers may be classes registered in the container, which allows
//            // for great testable, flexible composers for the application developer.
//            $factory->setContainer($app);
//
//            $factory->share('app', $app);
//
//            $factory->addExtension(
//                extension: 'phtml',
//                engine: 'LaminasEngine',
//                resolver: static fn () => $laminasEngine,
//            );
//
//            return $factory;
//        });
//
//        $rendererContainer->bind('view.finder', function ($app): FileViewFinder {
//            return new FileViewFinder($app['files'], $app['config']['view.paths']);
//        });
//
//        $rendererContainer->singleton('blade.compiler', function ($app): BladeCompiler {
//            $config = $app['config'];
//
//            return tap(new BladeCompiler(
//                $app['files'],
//                $config['view.compiled'],
//                $config->get('view.relative_hash', false) ? $app->basePath() : '',
//                $config->get('view.cache', true),
//                $config->get('view.compiled_extension', 'php'),
//            ), function ($blade) {
//                $blade->component('dynamic-component', DynamicComponent::class);
//            });
//        });
//
//        $rendererContainer->singleton('view.engine.resolver', function () use ($rendererContainer): EngineResolver {
//            $resolver = new EngineResolver();
//
//            // Next, we will register the various view engines with the resolver so that the
//            // environment will resolve the engines needed for various views based on the
//            // extension of view file. We call a method for each of the view's engines.
//            $resolver->register('file', function () use ($rendererContainer): FileEngine {
//                return new FileEngine($rendererContainer->make('files'));
//            });
//
//            $resolver->register('php', function () use ($rendererContainer): PhpEngine {
//                return new PhpEngine($rendererContainer->make('files'));
//            });
//
//            $resolver->register('blade', function () use ($rendererContainer): CompilerEngine {
//                $compiler = new CompilerEngine(
//                    $rendererContainer->make('blade.compiler'),
//                    $rendererContainer->make('files'),
//                );
//
//                return $compiler;
//            });
//
//            return $resolver;
//        });
//
//        $factory  = $rendererContainer->get('view');
//        $compiler = $rendererContainer->get('blade.compiler');

        // return new BladeRenderer(blade: $factory, compiler: $compiler, finder: $rendererContainer->get('view.finder'));

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

        if ($cachePath === '') {
            throw new ServiceNotCreatedException('a cache path is required');
        }

        $allPaths = isset($templateConfig['paths']) && is_array($templateConfig['paths'])
            ? $templateConfig['paths']
            : [];

        $rendererContainer = $container->get(\Mimmi20\Mezzio\BladeRenderer\Renderer\Container::class);

        Container::setInstance($rendererContainer);

        $blade = new Blade($allPaths, $cachePath, $rendererContainer);

        return new BladeRenderer(blade: $blade);
    }
}
