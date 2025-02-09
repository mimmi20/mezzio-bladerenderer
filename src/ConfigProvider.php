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

namespace Mimmi20\Mezzio\BladeRenderer;


use Laminas\ServiceManager\Factory\InvokableFactory;

use Mimmi20\Mezzio\BladeRenderer\Engine\LaminasEngine;
use Mimmi20\Mezzio\BladeRenderer\Engine\LaminasEngineFactory;
use Mimmi20\Mezzio\BladeRenderer\Renderer\BladeRenderer;
use Mimmi20\Mezzio\BladeRenderer\Renderer\BladeRendererFactory;
use Mimmi20\Mezzio\BladeRenderer\Renderer\Container;

final class ConfigProvider
{
    /**
     * @return array{dependencies: array{aliases: array<string, class-string>, factories: array<class-string|string, class-string>}, view_manager: array{default_template_suffix: string, strategies: array<int, class-string|string>}, blade: array{cache_dir: string}}
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
     */
    public function getDependencies(): array
    {
        return [
            'aliases' => [
                'renderer.blade' => BladeRenderer::class,
            ],
            'factories' => [
                BladeRenderer::class => BladeRendererFactory::class,
                Container::class => InvokableFactory::class,
                LaminasEngine::class => LaminasEngineFactory::class,
            ],
        ];
    }

    /**
     * @return array{default_template_suffix: string, strategies: array<int, class-string|string>}
     *
     * @throws void
     */
    public function getTemplates(): array
    {
        return [
            'cache-path' => '',
            'paths'     => [],
        ];
    }
}
