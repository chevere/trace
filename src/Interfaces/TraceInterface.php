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

use Chevere\Common\Interfaces\ToArrayInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Stringable;

/**
 * Describes the component in charge of defining a trace document.
 */
interface TraceInterface extends ToArrayInterface, Stringable
{
    public const TAG_ENTRY_FILE = '%file%';

    public const TAG_ENTRY_LINE = '%line%';

    public const TAG_ENTRY_FILE_LINE = '%fileLine%';

    public const TAG_ENTRY_CLASS = '%class%';

    public const TAG_ENTRY_TYPE = '%type%';

    public const TAG_ENTRY_FUNCTION = '%function%';

    public const TAG_ENTRY_CSS_EVEN_CLASS = '%cssEvenClass%';

    public const TAG_ENTRY_POS = '%pos%';

    public const ENTRY_EVEN = 'entry--even';

    public const HIGHLIGHT_TAGS = [
        self::TAG_ENTRY_FILE => VarDumperInterface::FILE,
        self::TAG_ENTRY_LINE => VarDumperInterface::FILE,
        self::TAG_ENTRY_FILE_LINE => VarDumperInterface::FILE,
        self::TAG_ENTRY_CLASS => VarDumperInterface::CLASS_REG,
        self::TAG_ENTRY_TYPE => VarDumperInterface::OPERATOR,
        self::TAG_ENTRY_FUNCTION => VarDumperInterface::FUNCTION,
    ];

    /**
     * @param array<array<string, mixed>> $trace
     */
    public function __construct(
        array $trace,
        FormatInterface $format
    );

    /**
     * @return array<array<string, string>>
     */
    public function table(): array;
}
