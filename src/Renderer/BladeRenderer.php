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
use Illuminate\Contracts\View\View;
use Illuminate\View\Compilers\BladeCompiler;
use Jenssegers\Blade\Blade;
use Mezzio\Template\ArrayParametersTrait;
use Mezzio\Template\DefaultParamsTrait;
use Mezzio\Template\Exception\InvalidArgumentException;
use Mezzio\Template\TemplatePath;
use Mezzio\Template\TemplateRendererInterface;
use Override;

use function assert;
use function is_iterable;
use function is_string;

final class BladeRenderer implements TemplateRendererInterface
{
    use ArrayParametersTrait;
    use DefaultParamsTrait;

    /** @throws void */
    public function __construct(private readonly Blade $blade)
    {
        // nothing to do
    }

    /**
     * Render a template, optionally with parameters.
     *
     * Implementations MUST support the `namespace::template` naming convention,
     * and allow omitting the filename extension.
     *
     * @param array<mixed>|object $params
     *
     * @throws InvalidArgumentException
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    #[Override]
    public function render(string $name, $params = []): string
    {
        return $this->blade->render($name, $this->normalizeParams($params));
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
        $this->blade->addLocation($path);
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

        $bladePaths = $this->blade->getPaths();
        assert(is_iterable($bladePaths));

        foreach ($bladePaths as $path) {
            if (!is_string($path)) {
                continue;
            }

            $paths[] = new TemplatePath($path);
        }

        return $paths;
    }

    /**
     * Register a view composer event.
     *
     * @param array<string>|string $views
     *
     * @return array<mixed>
     *
     * @throws void
     *
     * @api
     */
    public function composer(array | string $views, Closure | string $callback): array
    {
        return $this->blade->composer($views, $callback);
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
        return $this->blade->share($key, $value);
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
        return $this->blade->exists($view);
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
        return $this->blade->creator($views, $callback);
    }

    /**
     * @throws void
     *
     * @api
     */
    public function compiler(): BladeCompiler
    {
        return $this->blade->compiler();
    }
}
