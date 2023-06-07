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

namespace Chevere\Tests;

use Chevere\Trace\Formats\HtmlFormat;
use Chevere\Trace\Formats\PlainFormat;
use Chevere\Trace\Interfaces\TraceInterface;
use Chevere\Trace\Trace;
use PHPUnit\Framework\TestCase;
use stdClass;

final class TraceTest extends TestCase
{
    public function testPlainFormat(): void
    {
        $file = __FILE__;
        $line = 1;
        $class = 'Chevere\TestsTest';
        $type = '->';
        $function = 'testPlainFormat';
        $resource = fopen(__FILE__, 'r');
        $stdClass = new stdClass();
        $stdClassId = spl_object_id($stdClass);
        $resourceId = get_resource_id($resource);
        $debugBacktrace = [
            [
                'file' => $file,
                'line' => $line,
                'function' => $function,
                'class' => $class,
                'type' => '->',
                'args' => [true, 123, 'string', [], $stdClass, $resource],
            ],
        ];
        $trace = new Trace($debugBacktrace, new PlainFormat());
        $expected = <<<STRING
        ------------------------------------------------------------
        #0 {$file}:{$line}
        {$class}{$type}{$function}(bool(true), int(123), string(length=6), array(size=0), stdClass(#{$stdClassId}), resource (stream)({$resourceId}))
        ------------------------------------------------------------
        STRING;
        $this->assertSame($expected, strval($trace));
        $debugBacktrace[] = $debugBacktrace[0];
        $trace = new Trace($debugBacktrace, new PlainFormat());
        $this->assertSame(
            '',
            $trace->table()[0][TraceInterface::TAG_ENTRY_CSS_EVEN_CLASS]
        );
        $this->assertSame(
            TraceInterface::ENTRY_EVEN,
            $trace->table()[1][TraceInterface::TAG_ENTRY_CSS_EVEN_CLASS]
        );
    }

    public function testHtmlFormat(): void
    {
        $file = __FILE__;
        $line = 1;
        $class = 'Chevere\TestsTest';
        $type = '->';
        $function = 'testPlainFormat';
        $resource = fopen(__FILE__, 'r');
        $stdClass = new stdClass();
        $stdClassId = spl_object_id($stdClass);
        $resourceId = get_resource_id($resource);
        $debugBacktrace = [
            [
                'file' => $file,
                'line' => $line,
                'function' => $function,
                'class' => $class,
                'type' => '->',
                'args' => [true, 123, 'string', [], $stdClass, $resource],
            ],
        ];
        $format = new HtmlFormat();
        $trace = new Trace($debugBacktrace, $format);
        $fileLine = $format->varDumpFormat()->getHighlight(
            TraceInterface::HIGHLIGHT_TAGS[TraceInterface::TAG_ENTRY_FILE],
            "{$file}:{$line}"
        );
        $class = $format->varDumpFormat()->getHighlight(
            TraceInterface::HIGHLIGHT_TAGS[TraceInterface::TAG_ENTRY_CLASS],
            $class
        );
        $type = $format->varDumpFormat()->getHighlight(
            TraceInterface::HIGHLIGHT_TAGS[TraceInterface::TAG_ENTRY_TYPE],
            $type
        );
        $functionArgs = $format->varDumpFormat()->getHighlight(
            TraceInterface::HIGHLIGHT_TAGS[TraceInterface::TAG_ENTRY_FUNCTION],
            <<<STRING
            {$function}(bool(true), int(123), string(length=6), array(size=0), stdClass(#{$stdClassId}), resource (stream)({$resourceId}))
            STRING
        );
        $expected = <<<STRING
        ------------------------------------------------------------
        #0 {$fileLine}
        {$class}{$type}{$functionArgs}
        ------------------------------------------------------------
        STRING;
        $this->assertSame($expected, strval($trace));
    }
}
