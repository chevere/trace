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

namespace Chevere\Trace\Interfaces;

use Chevere\VarDump\Interfaces\FormatInterface as VarDumpFormatInterface;

/**
 * Describes the component in charge of defining a trace format.
 */
interface FormatInterface
{
    public function varDumpFormat(): VarDumpFormatInterface;

    public function getItemTemplate(): string;

    public function getHr(): string;

    public function getNewLine(): string;

    public function getWrapNewLine(string $text): string;

    public function getLineBreak(): string;

    public function getWrapLink(string $text): string;

    public function getWrapHidden(string $text): string;
}
