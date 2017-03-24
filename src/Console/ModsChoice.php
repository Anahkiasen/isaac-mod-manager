<?php

namespace Isaac\Console;

use Illuminate\Support\Collection;
use Isaac\Services\Mods\Mod;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ModsChoice extends ChoiceQuestion
{
    /**
     * @var Collection
     */
    protected $mods;

    /**
     * {@inheritdoc}
     */
    public function __construct($question, Collection $mods, bool $multiselect = true)
    {
        $this->mods = $mods;

        // Format choices in a human readable way
        $choices = $mods->map(function (Mod $mod) {
            return sprintf('%s (%s)', $mod->getName(), $mod->getId());
        });

        // Set multiselect or not
        if ($multiselect) {
            $question .= ' Can use multiple answers (eg. 1,2,4)';
        }

        parent::__construct($question, $choices->all());

        // Set correct validator
        $this->setMultiselect($multiselect);
        $this->setAutocompleterValues($mods->keys());
        $this->setValidator(new ChoiceValidator($this));
    }

    /**
     * @param int|int[] $answer
     *
     * @return Collection
     */
    public function getModsFromAnswer($answer): Collection
    {
        $answer = (array) $answer;

        /** @var Collection $modKeys */
        $modKeys = $this->mods->map->getId();
        foreach ($answer as &$choice) {
            $choice = $modKeys->get($choice);
        }

        return $this->mods->filter(function (Mod $mod) use ($answer) {
            return in_array($mod->getId(), $answer, true);
        });
    }

    /**
     * @param int|int[] $answer
     *
     * @return Collection|int[]
     */
    public function getModIdsFromAnswer($answer): Collection
    {
        return $this->getModsFromAnswer($answer)->map->getId();
    }
}
