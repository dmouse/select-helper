<?php

namespace Dmouse\Console\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Output\OutputInterface;

class SelectHelper extends Helper
{
    const CHARACTER = '>';
    const GO_TO_BEGIN_LINE = "\x0D";
    const MOVE_UP = "\033[1A";
    const MOVE_DOWN = "\033[1B";
    const COLOR_CHARACTER = "\033[1;32m";

    /**
     * @var array
     */
    protected $options;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var int
     */
    protected $totalOptions;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {

        $this->output = $output;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        $this->totalOptions = count($options);
    }

    /**
     * @return int
     */
    public function runSelect()
    {
        $this->prepareShell();
        $this->printOptions();
        $this->startCursor();

        $down = $this->totalOptions;
        $up = 0;


        while(!feof(STDIN)) {
            $c = fread(STDIN, 1);

            if ("\x1b" === $c) {
                $c = fread(STDIN, 1);
                if ("\x5b" === $c) {
                    $c = fread(STDIN, 1);
                    if ("\x41" === $c && $this->inArea($up)) {
                        $this->moveCursor(self::MOVE_UP);
                        $up++;
                        $down--;
                    }
                    else if ("\x42" === $c && $this->inArea($down - 1)) {
                        $this->moveCursor(self::MOVE_DOWN);
                        $down++;
                        $up--;
                    }
                }
            }
            else if("\n" === $c) {
                $keyOption = $down -1;
                $jump = $this->totalOptions - $down + 1;
                $this->output->writeln("\033[{$jump}B");
                $this->output->writeln("= ".$this->options[$keyOption]);
                return $keyOption;
            }
        }

        $this->restoreShell();
    }

    /**
     * @param int $position
     * @return bool
     */
    private function inArea($position)
    {
        return $position < $this->totalOptions - 1;
    }

    /**
     * First configuration to cursor.
     */
    private function startCursor()
    {
        $this->output->write(self::GO_TO_BEGIN_LINE);
        $this->printCharacter();
    }

    /**
     *
     */
    private function printCharacter()
    {
        $this->output->write(self::GO_TO_BEGIN_LINE);
        $this->output->write(self::COLOR_CHARACTER);
        $this->output->write(self::CHARACTER);
        $this->output->write(self::GO_TO_BEGIN_LINE);
    }

    /**
     * @param $position
     */
    private function moveCursor($position)
    {
        $this->output->write(" ");
        $this->output->write($position);
        $this->printCharacter();
    }

    /**
     * Print each option.
     */
    private function printOptions()
    {
        $last = end(array_keys($this->options));
        foreach($this->options as $key => $option) {
            if ($last == $key){
                $this->output->write(" [$key] $option");
            }
            else {
                $this->output->writeln(" [$key] $option");
            }
        }
    }

    /**
     * Prepare shell, disable cursor.
     */
    private function prepareShell()
    {
        $this->sttyMode = shell_exec('stty -g');
        shell_exec('stty -icanon -echo');
        $this->output->write("\033[?25l");
    }

    /**
     * Restore shell, enable cursor.
     */
    private function restoreShell()
    {
        shell_exec(sprintf('stty %s', $this->sttyMode));
        $this->output->write("\033[?25h");
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "select";
    }
}