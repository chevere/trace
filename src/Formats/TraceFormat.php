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

namespace Chevere\Trace\Formats;

use Chevere\Trace\Interfaces\TraceDocumentInterface;
use Chevere\Trace\Interfaces\TraceFormatInterface;
use Chevere\VarDump\Interfaces\VarDumpFormatInterface;

abstract class TraceFormat implements TraceFormatInterface
{
    final public function __construct()
    {
        $this->varDumpFormatter = $this->getVarDumpFormat();
    }

    final public function varDumpFormat(): VarDumpFormatInterface
    {
        return $this->varDumpFormatter;
    }

    abstract public function getVarDumpFormat(): VarDumpFormatInterface;

    public function getItemTemplate(): string
    {
        return '#' . TraceDocumentInterface::TAG_ENTRY_POS .
            ' ' . TraceDocumentInterface::TAG_ENTRY_FILE_LINE . "\n" .
            TraceDocumentInterface::TAG_ENTRY_CLASS .
            TraceDocumentInterface::TAG_ENTRY_TYPE .
            TraceDocumentInterface::TAG_ENTRY_FUNCTION;
    }

    public function getHr(): string
    {
        return '------------------------------------------------------------';
    }

    public function getNewLine(): string
    {
        return "\n";
    }

    final public function getWrapNewLine(string $text): string
    {
        return $this->getNewLine()
            . $text
            . $this->getNewLine();
    }

    final public function getLineBreak(): string
    {
        return $this->getNewLine()
            . $this->getNewLine();
    }

    public function getWrapLink(string $text): string
    {
        return $text;
    }

    public function getWrapHidden(string $text): string
    {
        return $text;
    }
}
