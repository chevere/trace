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

namespace Chevere\Trace;

use Chevere\Trace\Interfaces\EntryInterface;
use Chevere\Trace\Interfaces\FormatInterface;
use Chevere\Trace\Interfaces\TraceInterface;

final class Trace implements TraceInterface
{
    /**
     * @var array<string>
     */
    private array $array = [];

    /**
     * @var array<array<string, string>>
     */
    private array $table = [];

    private string $string = '';

    public function __construct(
        private array $trace,
        private FormatInterface $format
    ) {
        foreach ($this->trace as $pos => $entry) {
            $this->table[$pos] = $this->getTable($pos, new Entry($entry));
            $this->array[] = strtr(
                $this->format->getItemTemplate(),
                $this->table[$pos]
            );
        }
        if ($this->array !== []) {
            $this->string = $this->wrapStringHr(
                $this->glueString($this->array)
            );
        }
    }

    public function __toString(): string
    {
        return $this->string;
    }

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return $this->array;
    }

    public function table(): array
    {
        return $this->table;
    }

    /**
     * @return array<string, string>
     */
    private function getTable(
        int $pos,
        EntryInterface $entry
    ): array {
        $function = $this->getFunctionWithArguments($entry);
        $trValues = [
            self::TAG_ENTRY_CSS_EVEN_CLASS => ($pos & 1) !== 0
                ? self::ENTRY_EVEN : '',
            self::TAG_ENTRY_POS => strval($pos),
            self::TAG_ENTRY_FILE => $entry->file(),
            self::TAG_ENTRY_LINE => strval($entry->line()),
            self::TAG_ENTRY_FILE_LINE => $entry->fileLine(),
            self::TAG_ENTRY_CLASS => $entry->class(),
            self::TAG_ENTRY_TYPE => $entry->type(),
            self::TAG_ENTRY_FUNCTION => $function,
        ];
        $array = $trValues;
        foreach (self::HIGHLIGHT_TAGS as $tag => $key) {
            $val = $trValues[$tag];
            $array[$tag] = $this->format->varDumpFormat()
                ->getHighlight($key, strval($val));
        }

        return $array;
    }

    private function getFunctionWithArguments(
        EntryInterface $entry
    ): string {
        $return = [];
        foreach ($entry->args() as $argument) {
            $type = get_debug_type($argument);
            $value = match (true) {
                is_bool($argument) => $argument ? 'true' : 'false',
                is_numeric($argument) => strval($argument),
                is_string($argument) => 'length=' . strlen($argument),
                is_array($argument) => 'size=' . count($argument),
                is_object($argument) => '#'
                    . strval(spl_object_id($argument)),
                is_resource($argument) => get_resource_id($argument),
                default => '',
            };
            $return[] = $type
                . ($value !== '' ? "({$value})" : '');
        }
        $return = implode(', ', $return);

        return $entry->function()
            . "({$return})";
    }

    private function wrapStringHr(string $text): string
    {
        return $this->format->getHr()
            . $this->format->getWrapNewLine($text)
            . $this->format->getHr();
    }

    /**
     * @param array<string> $array
     */
    private function glueString(array $array): string
    {
        return implode(
            $this->format->getWrapNewLine($this->format->getHr()),
            $array
        );
    }
}
