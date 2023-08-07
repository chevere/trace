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

use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Trace\Interfaces\EntryInterface;
use ReflectionMethod;
use function Chevere\Message\message;

final class Entry implements EntryInterface
{
    private string $file;

    private int $line;

    private string $fileLine;

    private string $function;

    private string $class;

    private string $type;

    /**
     * @var array<int, mixed>
     */
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
            if (! array_key_exists($key, $this->entry)) {
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
        // @phpstan-ignore-next-line
        $this->line = $this->entry['line'] ?? 0;
        // @phpstan-ignore-next-line
        $this->args = $this->entry['args'] ?? [];
        foreach (array_diff(self::KEYS, ['line', 'args']) as $propName) {
            // @phpstan-ignore-next-line
            $this->{$propName} = $this->entry[$propName] ?? '';
        }
    }

    private function handleMissingClassFile(): void
    {
        if (! (
            $this->class !== ''
            && $this->file === ''
            && ! str_ends_with($this->function, '{closure}')
        )) {
            return;
        }
        $reflector = new ReflectionMethod($this->class, $this->function);
        $filename = $reflector->getFileName();
        if (is_string($filename)) {
            $this->file = $filename;
            /** @var int $line */
            $line = $reflector->getStartLine();
            $this->line = $line;
        }
    }

    private function handleAnonClass(): void
    {
        if (! str_starts_with($this->class, 'class@anonymous')) {
            return;
        }
        preg_match('#class@anonymous(.*):(\d+)#', $this->class, $matches);
        $this->class = 'class@anonymous';
        $this->file = $matches[1];
        $this->line = (int) $matches[2];
    }
}
