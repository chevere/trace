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

use Chevere\Tests\Trace\_resources\ExceptionClosure;
use Chevere\Trace\Entry;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Throwable;

final class TraceEntryTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Entry([]);
    }

    public function testConstructInvalidTypes(): void
    {
        $entry = [
            'file' => null,
            'line' => null,
            'function' => null,
            'class' => null,
            'type' => null,
        ];
        $traceEntry = new Entry($entry);
        $strings = [
            'file',
            'function',
            'class',
            'type',
        ];
        foreach ($strings as $method) {
            $this->assertSame('', $traceEntry->{$method}());
        }
        $this->assertSame([], $traceEntry->args());
        $this->assertSame(0, $traceEntry->line());
        $this->assertSame('', $traceEntry->fileLine());
    }

    public function testConstructTypes(): void
    {
        $filename = __FILE__;
        $line = 100;
        $entry = [
            'file' => $filename,
            'line' => $line,
            'function' => __FUNCTION__,
            'class' => __CLASS__,
            'type' => '->',
            'args' => [1, '2'],
        ];
        $traceEntry = new Entry($entry);
        foreach ($entry as $method => $val) {
            $this->assertSame($val, $traceEntry->{$method}());
        }
        $this->assertSame($line, $traceEntry->line());
        $this->assertSame($filename . ':' . $line, $traceEntry->fileLine());
    }

    public function testClassClosure(): void
    {
        try {
            new ExceptionClosure('test', 123);
        } catch (Throwable $e) {
            $entry = $e->getTrace()[0];
            $traceEntry = new Entry($entry);
            $this->assertSame(0, $traceEntry->line());
        }
    }

    public function testClassClosureSynthetic(): void
    {
        $entry = [
            'function' => '{closure}',
            'class' => __CLASS__,
            'type' => '::',
            'args' => [],
        ];
        $traceEntry = new Entry($entry);
        $this->assertSame(0, $traceEntry->line());
    }

    public function testAnonClass(): void
    {
        $line = __LINE__ + 1;
        $fileLine = __FILE__ . ':' . strval($line);
        $entry = [
            'file' => null,
            'line' => null,
            'function' => 'method',
            'class' => 'class@anonymous' . $fileLine . '$a3',
            'type' => '->',
            'args' => [],
        ];
        $traceEntry = new Entry($entry);
        $this->assertSame(
            'class@anonymous',
            $traceEntry->class()
        );
        $this->assertSame($line, $traceEntry->line());
        $this->assertSame($fileLine, $traceEntry->fileLine());
    }

    public function testMissingAnonClassFile(): void
    {
        $line = 100;
        $anonPath = '/path/to/file.php';
        $anonFileLine = $anonPath . ':' . strval($line);
        $entry = [
            'file' => null,
            'line' => null,
            'function' => __FUNCTION__,
            'class' => 'class@anonymous' . $anonFileLine . '$b5',
            'type' => '->',
        ];
        $traceEntry = new Entry($entry);
        $this->assertSame($line, $traceEntry->line());
        $this->assertSame($anonFileLine, $traceEntry->fileLine());
    }

    public function testMissingClassFile(): void
    {
        $line = __LINE__ - 2;
        $entry = [
            'file' => null,
            'line' => null,
            'function' => __FUNCTION__,
            'class' => __CLASS__,
            'type' => '->',
            'args' => [1, '2'],
        ];
        $traceEntry = new Entry($entry);
        $this->assertSame($line, $traceEntry->line());
        $this->assertSame(__FILE__ . ':' . $line, $traceEntry->fileLine());
    }

    public function testMissingFileLine(): void
    {
        $entry = [
            'file' => 'duh',
            'line' => null,
            'function' => '',
            'class' => '',
            'type' => '->',
        ];
        $traceEntry = new Entry($entry);
        $this->assertSame(0, $traceEntry->line());
    }
}
