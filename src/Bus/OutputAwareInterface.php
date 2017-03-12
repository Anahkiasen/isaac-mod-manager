<?php

namespace Isaac\Bus;

use Symfony\Component\Console\Style\SymfonyStyle;

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