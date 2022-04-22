<?php

namespace percipiolondon\staff\helpers;

use craft\console\Application as ConsoleApplication;
use yii\helpers\BaseConsole;
use yii\helpers\Console;

class Logger
{
    // foreground color control codes
    //const FG_BLACK = 30;
    public const FG_RED = 31;
    public const FG_GREEN = 32;
    public const FG_YELLOW = 33;
    //const FG_BLUE = 34;
    public const FG_PURPLE = 35;
    //const FG_CYAN = 36;
    //const FG_GREY = 37;
    // background color control codes
    //const BG_BLACK = 40;
    //const BG_RED = 41;
    //const BG_GREEN = 42;
    //const BG_YELLOW = 43;
    //const BG_BLUE = 44;
    //const BG_PURPLE = 45;
    //const BG_CYAN = 46;
    //const BG_GREY = 47;
    // fonts style control codes
    public const RESET = 0;
    //const NORMAL = 0;
    //const BOLD = 1;
    //const ITALIC = 3;
    //const UNDERLINE = 4;
    //const BLINK = 5;
    //const NEGATIVE = 7;
    //const CONCEALED = 8;
    //const CROSSED_OUT = 9;
    //const FRAMED = 51;
    //const ENCIRCLED = 52;
    //const OVERLINED = 53;

    /**
     * @var bool|null whether to enable ANSI color in the output.
     * If not set, ANSI color will only be enabled for terminals that support it.
     */
    public ?bool $color;

    /**
     * Formats a string with ANSI codes.
     *
     * You may pass additional parameters using the constants defined in [[\yii\helpers\Console]].
     *
     * Example:
     *
     * ```
     * echo $this->ansiFormat('This will be red and underlined.', Console::FG_RED, Console::UNDERLINE);
     * ```
     *
     * @param string $string the string to be formatted
     * @return string
     */
    public function ansiFormat(string $string): string
    {
        if ($this->isColorEnabled()) {
            $args = func_get_args();
            array_shift($args);
            $string = BaseConsole::ansiFormat($string, $args);
        }

        return $string;
    }

    /**
     * Prints a string to STDOUT.
     *
     * You may optionally format the string with ANSI codes by
     * passing additional parameters using the constants defined in [[\yii\helpers\Console]].
     *
     * Example:
     *
     * ```
     * $this->stdout('This will be red and underlined.', Console::FG_RED, Console::UNDERLINE);
     * ```
     *
     * @param string $string the string to print
     * @return int|bool Number of bytes printed or false on error
     */
    public function stdout(string $string): bool|int
    {
        if (\Craft::$app instanceof ConsoleApplication) {
            if ($this->isColorEnabled()) {
                $args = func_get_args();
                array_shift($args);
                $string = BaseConsole::ansiFormat($string, $args);
            }

            return BaseConsole::stdout($string);
        }

        return false;
    }

    /**
     * Prints a string to STDERR.
     *
     * You may optionally format the string with ANSI codes by
     * passing additional parameters using the constants defined in [[\yii\helpers\Console]].
     *
     * Example:
     *
     * ```
     * $this->stderr('This will be red and underlined.', Console::FG_RED, Console::UNDERLINE);
     * ```
     *
     * @param string $string the string to print
     * @return int|bool Number of bytes printed or false on error
     */
    public function stderr(string $string): bool|int
    {
        if ($this->isColorEnabled(\STDERR)) {
            $args = func_get_args();
            array_shift($args);
            $string = BaseConsole::ansiFormat($string, $args);
        }

        return fwrite(\STDERR, $string);
    }

    /**
     * Returns a value indicating whether ANSI color is enabled.
     *
     * ANSI color is enabled only if [[color]] is set true or is not set
     * and the terminal supports ANSI color.
     *
     * @param mixed $stream the stream to check.
     * @return bool Whether to enable ANSI style in output.
     */
    public function isColorEnabled(mixed $stream = null): ?bool
    {
        return $this->color ?? BaseConsole::streamSupportsAnsiColors($stream ?? \STDOUT);
    }
}
