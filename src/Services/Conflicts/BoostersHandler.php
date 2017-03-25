<?php

namespace Isaac\Services\Conflicts;

use Illuminate\Support\Collection;
use Isaac\Bus\OutputAwareInterface;
use Isaac\Bus\OutputAwareTrait;
use Isaac\Services\Mods\Mod;

/**
 * Filter out mods that were in booster packs if the
 * user is ok with that.
 */
class BoostersHandler implements OutputAwareInterface
{
    use OutputAwareTrait;

    /**
     * @var int[]
     */
    const BOOSTED = [
        834446972,
        835149267,
        835177566,
        835885114,
        835997271,
        837433281,
        839519212,
        839883727,
        840361451,
        840640979,
        841132488,
        841401139,
        842444127,
        844440401,
        845230668,
    ];

    /**
     * @param Collection $mods
     *
     * @return Collection
     */
    public function filterBoosted(Collection $mods): Collection
    {
        $boosted = $mods->filter(function (Mod $mod) {
            return in_array($mod->getId(), static::BOOSTED, true);
        });

        // If we found mods that were in booster pack, ask the user if he wants to
        // install them anyway
        if ($boosted->count()) {
            if ($this->getOutput()->confirm('Your modlist contains mods that were added in Booster packs, do you want to ignore those?')) {
                return $mods->diffKeys($boosted);
            }
        }

        return $mods;
    }
}
