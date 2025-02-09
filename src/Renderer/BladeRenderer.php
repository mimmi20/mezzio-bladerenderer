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

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use InvalidArgumentException;
use Mezzio\Template\ArrayParametersTrait;
use Mezzio\Template\DefaultParamsTrait;
use Mezzio\Template\TemplatePath;
use Mezzio\Template\TemplateRendererInterface;
use Override;

use function is_string;

final class BladeRenderer implements TemplateRendererInterface
{
    use ArrayParametersTrait;
    use DefaultParamsTrait;

    /** @throws void */
    public function __construct(
        private readonly Factory $factory,
        private readonly BladeCompiler $compiler,
        private readonly FileViewFinder $finder,
    ) {
        // nothing to do
    }

    /**
     * @param array<mixed> $params
     *
     * @throws void
     */
    public function __call(string $method, array $params): mixed
    {
        return $this->factory->{$method}(...$params);
    }

    /**
     * Render a template, optionally with parameters.
     *
     * Implementations MUST support the `namespace::template` naming convention,
     * and allow omitting the filename extension.
     *
     * @param array<mixed>|object $params
     *
     * @throws \Mezzio\Template\Exception\InvalidArgumentException
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
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
     * Add a template path to the engine.
     *
     * Adds a template path, with optional namespace the templates in that path
     * provide.
     *
     * @throws void
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    #[Override]
    public function addPath(string $path, string | null $namespace = null): void
    {
        $this->finder->addLocation($path);
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

        foreach ($this->finder->getPaths() as $path) {
            if (!is_string($path)) {
                continue;
            }

            $paths[] = new TemplatePath($path);
        }

        return $paths;
    }

    /**
     * @throws void
     *
     * @api
     */
    public function compiler(): BladeCompiler
    {
        return $this->compiler;
    }

    /**
     * Register a handler for custom directives.
     *
     * @param (callable(): string) $handler
     *
     * @throws InvalidArgumentException
     *
     * @api
     */
    public function directive(string $name, callable $handler): void
    {
        $this->compiler->directive($name, $handler);
    }

    /**
     * Register an "if" statement directive.
     *
     * @param (callable(): bool) $callback
     *
     * @throws void
     *
     * @api
     */
    public function if(string $name, callable $callback): void
    {
        $this->compiler->if($name, $callback);
    }

    /**
     * Determine if a given view exists.
     *
     * @throws void
     *
     * @api
     */
    public function exists(string $view): bool
    {
        return $this->factory->exists($view);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param array<int|string, mixed>|Arrayable<int|string, mixed> $data
     * @param array<mixed>                                          $mergeData
     *
     * @throws void
     *
     * @api
     */
    public function file(string $path, Arrayable | array $data = [], array $mergeData = []): View
    {
        return $this->factory->file($path, $data, $mergeData);
    }

    /**
     * Add a piece of shared data to the environment.
     *
     * @param array<mixed>|string $key
     *
     * @throws void
     *
     * @api
     */
    public function share(array | string $key, mixed $value = null): mixed
    {
        return $this->factory->share($key, $value);
    }

    /**
     * Register a view composer event.
     *
     * @param array<string>|string         $views
     * @param (Closure(View): void)|string $callback
     *
     * @return array<mixed>
     *
     * @throws void
     *
     * @api
     */
    public function composer(array | string $views, Closure | string $callback): array
    {
        return $this->factory->composer($views, $callback);
    }

    /**
     * Register a view creator event.
     *
     * @param array<string>|string         $views
     * @param (Closure(View): void)|string $callback
     *
     * @return array<mixed>
     *
     * @throws void
     *
     * @api
     */
    public function creator(array | string $views, Closure | string $callback): array
    {
        return $this->factory->creator($views, $callback);
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param array<string>|string $hints
     *
     * @throws void
     *
     * @api
     */
    public function addNamespace(string $namespace, string | array $hints): self
    {
        $this->factory->addNamespace($namespace, $hints);

        return $this;
    }

    /**
     * Replace the namespace hints for the given namespace.
     *
     * @param array<string>|string $hints
     *
     * @throws void
     *
     * @api
     */
    public function replaceNamespace(string $namespace, string | array $hints): self
    {
        $this->factory->replaceNamespace($namespace, $hints);

        return $this;
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param array<int|string, mixed>|Arrayable<int|string, mixed> $data
     * @param array<mixed>                                          $mergeData
     *
     * @throws void
     */
    private function make(string $view, Arrayable | array $data = [], array $mergeData = []): View
    {
        return $this->factory->make($view, $data, $mergeData);
    }
}
