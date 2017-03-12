<?php

namespace Isaac\Bus;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Interface for an output-aware command.
 */
interface OutputAwareInterface
{
    /**
     * @param SymfonyStyle $output
     */
    public function setOutput(SymfonyStyle $output);

    /**
     * @return SymfonyStyle
     */
    public function getOutput(): SymfonyStyle;
}
