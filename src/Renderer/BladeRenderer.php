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

use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container as ContainerInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Factory;
use Illuminate\View\ViewServiceProvider;
use Mezzio\Template\ArrayParametersTrait;
use Mezzio\Template\DefaultParamsTrait;
use Mezzio\Template\TemplatePath;
use Mezzio\Template\TemplateRendererInterface;
use Mimmi20\Mezzio\BladeRenderer\Engine\LaminasEngine;
use Override;

final class BladeRenderer implements TemplateRendererInterface
{
    use ArrayParametersTrait;
    use DefaultParamsTrait;

    /**
     * @var Application
     */
    protected $container;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var BladeCompiler
     */
    private $compiler;

    /** @throws void */
    public function __construct(array $viewPaths, string $cachePath, LaminasEngine $laminasEngine, ContainerInterface $container = null)
    {
        $this->container = $container ?: new Container();

        $this->container->bindIf('files', fn () => new Filesystem);
        $this->container->bindIf('events', fn () => new Dispatcher);
        $this->container->bindIf('config', fn () => new Repository([
            'view.paths' => $viewPaths,
            'view.compiled' => $cachePath,
        ]));

        Facade::setFacadeApplication($this->container);
        (new ViewServiceProvider($this->container))->register();

        $this->factory = $this->container->get('view');
        $this->factory->addExtension(
            extension: 'phtml',
            engine: 'LaminasEngine',
            resolver: function() use($laminasEngine) {return $laminasEngine;},
        );

        $this->compiler = $this->container->get('blade.compiler');
    }

    /**
     * Render a template, optionally with parameters.
     *
     * Implementations MUST support the `namespace::template` naming convention,
     * and allow omitting the filename extension.
     *
     * @param array|object $params
     */
    #[Override]
    public function render(string $name, $params = []): string
    {
        // Merge parameters based on requested template name
        $params = $this->mergeParams($name, $this->normalizeParams($params));

        // Merge parameters based on normalized template name
        $params = $this->mergeParams($name, $params);

        return $this->make($name, $params)->render();
    }

    /**
     * @param string $path
     * @param string|null $namespace
     * @return void
     * @throws void
     */
    #[Override]
    public function addPath(string $path, ?string $namespace = null): void
    {
        $this->factory->getFinder()->addLocation($path);
    }

    /**
     * Retrieve configured paths from the engine.
     *
     * @return array<int, TemplatePath>
     *
     * @throws void
     */
    #[Override]
    public function getPaths(): array
    {
        $paths = [];

        foreach ($this->factory->getFinder()->getPaths() as $path) {
            $paths[] = new TemplatePath($path);
        }

        return $paths;
    }

    /**
     * @return BladeCompiler
     */
    public function compiler(): BladeCompiler
    {
        return $this->compiler;
    }

    /**
     * Register a handler for custom directives.
     *
     * @param  string  $name
     * @param  callable  $handler
     * @param  bool  $bind
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function directive(string $name, callable $handler): void
    {
        $this->compiler->directive($name, $handler);
    }

    /**
     * Register an "if" statement directive.
     *
     * @param  string  $name
     * @param  callable  $callback
     * @return void
     */
    public function if($name, callable $callback): void
    {
        $this->compiler->if($name, $callback);
    }

    /**
     * Determine if a given view exists.
     *
     * @param  string  $view
     * @return bool
     */
    public function exists(string $view): bool
    {
        return $this->factory->exists($view);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $path
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @return \Illuminate\Contracts\View\View
     */
    public function file($path, $data = [], $mergeData = []): View
    {
        return $this->factory->file($path, $data, $mergeData);
    }

    /**
     * Add a piece of shared data to the environment.
     *
     * @param  array|string  $key
     * @param  mixed|null  $value
     * @return mixed
     */
    public function share($key, $value = null)
    {
        return $this->factory->share($key, $value);
    }

    /**
     * Register a view composer event.
     *
     * @param  array|string  $views
     * @param  \Closure|string  $callback
     * @return array
     */
    public function composer($views, $callback): array
    {
        return $this->factory->composer($views, $callback);
    }

    /**
     * Register a view creator event.
     *
     * @param  array|string  $views
     * @param  \Closure|string  $callback
     * @return array
     */
    public function creator($views, $callback): array
    {
        return $this->factory->creator($views, $callback);
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return $this
     */
    public function addNamespace($namespace, $hints): self
    {
        $this->factory->addNamespace($namespace, $hints);

        return $this;
    }

    /**
     * Replace the namespace hints for the given namespace.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return $this
     */
    public function replaceNamespace($namespace, $hints): self
    {
        $this->factory->replaceNamespace($namespace, $hints);

        return $this;
    }

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function __call(string $method, array $params)
    {
        return call_user_func_array([$this->factory, $method], $params);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @return \Illuminate\Contracts\View\View
     */
    private function make($view, $data = [], $mergeData = []): View
    {
        return $this->factory->make($view, $data, $mergeData);
    }
}
