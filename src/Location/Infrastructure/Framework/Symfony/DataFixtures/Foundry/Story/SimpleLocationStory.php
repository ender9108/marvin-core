<?php

namespace Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story;

use Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\ZoneFactory;
use Zenstruck\Foundry\Story;

final class SimpleLocationStory extends Story
{
    public function build(): void
    {
        // Pièces principales
        $salon = ZoneFactory::new()
            ->livingRoom()
            ->comfortable()
            ->create();

        $cuisine = ZoneFactory::new()
            ->kitchen()
            ->withAllMetrics()
            ->create();

        $chambre = ZoneFactory::new()
            ->bedroom()
            ->underheated()
            ->create();

        $bureau = ZoneFactory::new()
            ->office()
            ->overheated()
            ->create();

        $salledebain = ZoneFactory::new()
            ->bathroom()
            ->tooHumid()
            ->create();

        // Enregistrer dans le pool
        $this->addState('salon', $salon->_real());
        $this->addState('cuisine', $cuisine->_real());
        $this->addState('chambre', $chambre->_real());
        $this->addState('bureau', $bureau->_real());
        $this->addState('salledebain', $salledebain->_real());
    }
}
