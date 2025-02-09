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

namespace Mimmi20\Mezzio\BladeRenderer\Engine;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Mimmi20\Mezzio\BladeRenderer\Renderer\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class CompilerEngineFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): CompilerEngine
    {
        $blade             = $container->get(BladeCompiler::class);
        $rendererContainer = $container->get(Container::class);

        $compiler = new CompilerEngine(
            compiler: $blade,
            files: $container->get(Filesystem::class),
        );

        $rendererContainer->terminating(static function () use ($compiler): void {
            $compiler->forgetCompiledOrNotExpired();
        });

        return $compiler;
    }
}
