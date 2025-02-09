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

use Mimmi20\Mezzio\BladeRenderer\Engine\LaminasEngine;
use Mimmi20\Mezzio\BladeRenderer\Renderer\BladeRenderer;
use Mimmi20\Mezzio\BladeRenderer\Renderer\Container;
use Mimmi20\Mezzio\BladeRenderer\Strategy\BladeStrategy;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    private ConfigProvider $provider;

    /** @throws void */
    #[Override]
    protected function setUp(): void
    {
        $this->provider = new ConfigProvider();
    }

    /** @throws Exception */
    public function testGetDependencies(): void
    {
        $dependencies = $this->provider->getDependencies();
        self::assertIsArray($dependencies);

        self::assertArrayHasKey('factories', $dependencies);
        $factories = $dependencies['factories'];
        self::assertIsArray($factories);
        self::assertArrayHasKey(BladeRenderer::class, $factories);
        self::assertArrayHasKey(Container::class, $factories);
        self::assertArrayHasKey(LaminasEngine::class, $factories);

        self::assertArrayHasKey('aliases', $dependencies);
        $aliases = $dependencies['aliases'];
        self::assertIsArray($aliases);
        self::assertArrayHasKey('renderer.blade', $aliases);
    }

    /** @throws Exception */
    public function testGetViewManagerConfig(): void
    {
        $templateConfig = $this->provider->getTemplates();
        self::assertIsArray($templateConfig);

        self::assertArrayHasKey('cache-path', $templateConfig);
        self::assertArrayHasKey('paths', $templateConfig);
    }

    /** @throws Exception */
    public function testInvocationReturnsArrayWithDependencies(): void
    {
        $config = ($this->provider)();

        self::assertIsArray($config);
        self::assertArrayHasKey('dependencies', $config);
        self::assertArrayHasKey('templates', $config);

        $dependencies = $config['dependencies'];
        self::assertIsArray($dependencies);

        self::assertArrayHasKey('factories', $dependencies);
        $factories = $dependencies['factories'];
        self::assertIsArray($factories);
        self::assertArrayHasKey(BladeRenderer::class, $factories);
        self::assertArrayHasKey(Container::class, $factories);
        self::assertArrayHasKey(LaminasEngine::class, $factories);

        self::assertArrayHasKey('aliases', $dependencies);
        $aliases = $dependencies['aliases'];
        self::assertIsArray($aliases);
        self::assertArrayHasKey('renderer.blade', $aliases);

        $templateConfig = $config['templates'];
        self::assertIsArray($templateConfig);

        self::assertArrayHasKey('cache-path', $templateConfig);
        self::assertArrayHasKey('paths', $templateConfig);
    }
}
