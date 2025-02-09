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
use Illuminate\Container\Container as BaseContainer;

final class Container extends BaseContainer
{
    /** @var array<int, (Closure(): void)> */
    protected array $terminatingCallbacks = [];

    /**
     * @param (Closure(): void) $callback
     *
     * @throws void
     *
     * @api
     */
    public function terminating(Closure $callback): self
    {
        $this->terminatingCallbacks[] = $callback;

        return $this;
    }

    /**
     * @throws void
     *
     * @api
     */
    public function terminate(): void
    {
        foreach ($this->terminatingCallbacks as $terminatingCallback) {
            $terminatingCallback();
        }
    }

    /**
     * @throws void
     *
     * @api
     */
    public function getNamespace(): string
    {
        return '';
    }
}
