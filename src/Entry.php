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

use function Chevere\Message\message;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Trace\Interfaces\EntryInterface;
use ReflectionMethod;

final class Entry implements EntryInterface
{
    private string $file;

    private int $line;

    private string $fileLine;

    private string $function;

    private string $class;

    private string $type;

    private array $args;

    public function __construct(
        private array $entry
    ) {
        $this->assertEntry();
        $this->processEntry();
        $this->handleAnonClass();
        $this->handleMissingClassFile();
        if ($this->file === '') {
            $this->fileLine = '';
            $this->line = 0;
        } else {
            $this->fileLine = $this->file . ':' . $this->line;
        }
    }

    public function file(): string
    {
        return $this->file;
    }

    public function line(): int
    {
        return $this->line;
    }

    public function fileLine(): string
    {
        return $this->fileLine;
    }

    public function function(): string
    {
        return $this->function;
    }

    public function class(): string
    {
        return $this->class;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function args(): array
    {
        return $this->args;
    }

    private function assertEntry(): void
    {
        $missing = [];
        foreach (self::MUST_HAVE_KEYS as $key) {
            if (!array_key_exists($key, $this->entry)) {
                $missing[] = $key;
            }
        }
        if ($missing !== []) {
            throw new InvalidArgumentException(
                message('Missing key(s) %keyNames%')
                    ->withCode('%keyNames%', implode(', ', $missing))
            );
        }
    }

    private function processEntry(): void
    {
        $this->line = $this->entry['line'] ?? 0;
        $this->args = $this->entry['args'] ?? [];
        foreach (array_diff(self::KEYS, ['line', 'args']) as $propName) {
            $this->{$propName} = $this->entry[$propName] ?? '';
        }
    }

    private function handleMissingClassFile()
    {
        if (
            $this->class !== ''
            and $this->file === ''
            and !str_ends_with($this->function, '{closure}')
        ) {
            /** @var class-string $this->class */
            $reflector = new ReflectionMethod($this->class, $this->function);
            $filename = $reflector->getFileName();
            if ($filename) {
                $this->file = $filename;
                $this->line = $reflector->getStartLine();
            }
        }
    }

    private function handleAnonClass()
    {
        if (str_starts_with($this->class, 'class@anonymous')) {
            preg_match('#class@anonymous(.*):(\d+)#', $this->class, $matches);
            $this->class = 'class@anonymous';
            $this->file = $matches[1];
            $this->line = (int) $matches[2];
        }
    }
}
