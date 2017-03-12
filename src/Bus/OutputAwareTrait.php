<?php

namespace Isaac\Bus;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

trait OutputAwareTrait
{
    /**
     * @var SymfonyStyle
     */
    protected $output;

    /**
     * @return SymfonyStyle
     */
    public function getOutput(): SymfonyStyle
    {
        return $this->output ?: new SymfonyStyle(new ArrayInput([]), new NullOutput());
    }

    /**
     * @param SymfonyStyle $output
     */
    public function setOutput(SymfonyStyle $output)
    {
        $this->output = $output;
    }
}
