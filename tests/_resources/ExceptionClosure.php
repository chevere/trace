<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\_resources;

use Exception;
use Throwable;

final class ExceptionClosure extends Exception
{
    public function __construct(string $message, int $code, Throwable $previous = null)
    {
        $callable = static function () {
            throw new parent(...func_get_args());
        };
        call_user_func($callable);
    }
}
