<?php

namespace Isaac\Services\Conflicts;

use Illuminate\Support\Collection;
use Isaac\TestCase;
use Prophecy\Argument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BoostersHandlerTest extends TestCase
{
    public function testCanFilterBoostedMods()
    {
        $mod = $this->mockMod(834446972, 'Extra Eden Hairstyles');
        $mods = $this->mods->getMods()->push($mod);

        $output = $this->prophesize(SymfonyStyle::class);
        $output->confirm(Argument::cetera())->willReturn(true)->shouldBeCalled();

        $boosters = new BoostersHandler();
        $boosters->setOutput($output->reveal());

        $result = $boosters->filterBoosted($mods);

        $this->assertNotContains(834446972, $result->pluck('id')->all());
    }
}