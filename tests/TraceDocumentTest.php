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

namespace Chevere\Tests\Trace;

use Chevere\Trace\Formats\TraceFormatPlain;
use Chevere\Trace\TraceDocument;
use PHPUnit\Framework\TestCase;

final class TraceDocumentTest extends TestCase
{
    public function testConstruct(): void
    {
        $file = 'file';
        $line = 1;
        $function = 'function';
        $trace = [
            [
                'file' => $file,
                'line' => 1,
                'function' => $function,
                'args' => [],
            ],
        ];
        $format = new TraceFormatPlain();
        $document = new TraceDocument($trace, $format);
        $expect = "#0 $file:$line\n$function()";
        $this->assertSame(
            $document->toArray(),
            [
                0 => $expect
            ]
        );
        $this->assertSame(
            $format->getHr()
                . $format->getNewLine()
                . $expect
                . $format->getNewLine()
                . $format->getHr(),
            $document->__toString()
        );
    }

    public function testGetFunctionWithArguments(): void
    {
        $arguments = [
            0 => true,
            1 => false,
            2 => null,
            3 => 1,
            4 => 1.1,
            5 => 'cadena',
            6 => [],
            7 => [0, 1]
        ];
        $debugArguments = [
            0 => 'bool(true)',
            1 => 'bool(false)',
            2 => 'null',
            3 => 'int(1)',
            4 => 'float(1.1)',
            5 => 'string(length=6)',
            6 => 'array(size=0)',
            7 => 'array(size=2)'
        ];
        $trace = [
            [
                'file' => null,
                'line' => null,
                'function' => null,
                'args' => $arguments
            ]
        ];
        $format = new TraceFormatPlain();
        $document = new TraceDocument($trace, $format);
        $this->assertSame(
            '#0 '
                . $format->getNewLine()
                . '(' . implode(', ', $debugArguments) . ')',
            $document->toArray()[0]
        );
    }
}
