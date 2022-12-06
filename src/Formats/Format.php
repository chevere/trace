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

use Chevere\Trace\Interfaces\FormatInterface;
use Chevere\Trace\Interfaces\TraceInterface;
use Chevere\VarDump\Interfaces\FormatInterface as VarDumpFormatInterface;

abstract class Format implements FormatInterface
{
    private VarDumpFormatInterface $varDumpFormat;

    final public function __construct()
    {
        $this->varDumpFormat = $this->getVarDumpFormat();
    }

    final public function varDumpFormat(): VarDumpFormatInterface
    {
        return $this->varDumpFormat;
    }

    abstract public function getVarDumpFormat(): VarDumpFormatInterface;

    // @infection-ignore-all
    public function getItemTemplate(): string
    {
        return '#'
            . TraceInterface::TAG_ENTRY_POS
            . ' '
            . TraceInterface::TAG_ENTRY_FILE_LINE
            . "\n"
            . TraceInterface::TAG_ENTRY_CLASS
            . TraceInterface::TAG_ENTRY_TYPE
            . TraceInterface::TAG_ENTRY_FUNCTION;
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
        return $this->getNewLine() . $this->getNewLine();
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
