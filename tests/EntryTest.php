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

final class EntryTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Entry([]);
    }

    public function testConstructInvalidTypes(): void
    {
        $array = [
            'file' => null,
            'line' => null,
            'function' => null,
            'class' => null,
            'type' => null,
        ];
        $entry = new Entry($array);
        $strings = [
            'file',
            'function',
            'class',
            'type',
        ];
        foreach ($strings as $method) {
            $this->assertSame('', $entry->{$method}());
        }
        $this->assertSame([], $entry->args());
        $this->assertSame('', $entry->file());
        $this->assertSame(0, $entry->line());
        $this->assertSame('', $entry->fileLine());
    }

    public function testConstructTypes(): void
    {
        $filename = __FILE__;
        $line = 100;
        $array = [
            'file' => $filename,
            'line' => $line,
            'function' => __FUNCTION__,
            'class' => __CLASS__,
            'type' => '->',
            'args' => [1, '2'],
        ];
        $entry = new Entry($array);
        foreach ($array as $method => $val) {
            $this->assertSame($val, $entry->{$method}());
        }
        $this->assertSame($line, $entry->line());
        $this->assertSame($filename . ':' . $line, $entry->fileLine());
    }

    public function testClassClosure(): void
    {
        try {
            new ExceptionClosure('test', 123);
        } catch (Throwable $e) {
            $array = $e->getTrace()[0];
            $entry = new Entry($array);
            $this->assertSame(0, $entry->line());
        }
    }

    public function testClassClosureSynthetic(): void
    {
        $array = [
            'function' => '{closure}',
            'class' => __CLASS__,
            'type' => '::',
            'args' => [],
        ];
        $entry = new Entry($array);
        $this->assertSame(0, $entry->line());
    }

    public function testAnonClass(): void
    {
        $line = __LINE__ + 1;
        $fileLine = __FILE__ . ':' . strval($line);
        $array = [
            'file' => null,
            'line' => null,
            'function' => 'method',
            'class' => 'class@anonymous' . $fileLine . '$a3',
            'type' => '->',
            'args' => [],
        ];
        $entry = new Entry($array);
        $this->assertSame(
            'class@anonymous',
            $entry->class()
        );
        $this->assertSame($line, $entry->line());
        $this->assertSame($fileLine, $entry->fileLine());
    }

    public function testMissingAnonClassFile(): void
    {
        $line = 100;
        $anonPath = '/path/to/file.php';
        $anonFileLine = $anonPath . ':' . strval($line);
        $array = [
            'file' => null,
            'line' => null,
            'function' => __FUNCTION__,
            'class' => 'class@anonymous' . $anonFileLine . '$b5',
            'type' => '->',
        ];
        $entry = new Entry($array);
        $this->assertSame($line, $entry->line());
        $this->assertSame($anonFileLine, $entry->fileLine());
    }

    public function testMissingClassFile(): void
    {
        $line = __LINE__ - 2;
        $array = [
            'file' => null,
            'line' => null,
            'function' => __FUNCTION__,
            'class' => __CLASS__,
            'type' => '->',
            'args' => [1, '2'],
        ];
        $entry = new Entry($array);
        $this->assertSame($line, $entry->line());
        $this->assertSame(__FILE__ . ':' . $line, $entry->fileLine());
    }

    public function testMissingFileLine(): void
    {
        $array = [
            'file' => 'duh',
            'line' => null,
            'function' => '',
            'class' => '',
            'type' => '->',
        ];
        $entry = new Entry($array);
        $this->assertSame(0, $entry->line());
    }
}
