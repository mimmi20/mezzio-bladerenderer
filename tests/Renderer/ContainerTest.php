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

use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
    private Container $container;

    /** @throws void */
    #[Override]
    protected function setUp(): void
    {
        $this->container = Container::getInstance();
    }

    /** @throws Exception */
    public function testGetNamespace(): void
    {
        self::assertSame('', $this->container->getNamespace());
    }

    /** @throws Exception */
    public function testTerminating(): void
    {
        $counter  = 0;
        $callback = static function () use (&$counter): void {
            ++$counter;
        };

        $this->container->terminating($callback);
        $this->container->terminate();

        self::assertSame(1, $counter);
    }
}
