<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story;

use Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\ZoneFactory;
use Zenstruck\Foundry\Story;

class LocationStory extends Story
{
    public function build(): void
    {
        $maison = ZoneFactory::new()
            ->building()
            ->withTemperature(20.5)
            ->create();

        $salon = ZoneFactory::new()
            ->livingRoom()
            ->withParent($maison->_real())
            ->occupied()
            ->withTemperature(21.0)
            ->withPowerConsumption(450.0)
            ->create();

        $cuisine = ZoneFactory::new()
            ->kitchen()
            ->withParent($maison->_real())
            ->withTemperature(20.0)
            ->withPowerConsumption(800.0)
            ->create();

        $chambre = ZoneFactory::new()
            ->bedroom()
            ->withParent($maison->_real())
            ->unoccupied()
            ->withTemperature(19.0)
            ->withPowerConsumption(50.0)
            ->create();

        $salleDeBain = ZoneFactory::new()
            ->bathroom()
            ->withParent($maison->_real())
            ->withTemperature(22.0)
            ->withPowerConsumption(150.0)
            ->create();

        $jardin = ZoneFactory::new()
            ->garden()
            ->withParent($maison->_real())
            ->create();

        $garage = ZoneFactory::new()
            ->garage()
            ->withParent($maison->_real())
            ->withTemperature(15.0)
            ->create();

        $this->addState('maison', $maison);
        $this->addState('salon', $salon);
        $this->addState('cuisine', $cuisine);
        $this->addState('chambre', $chambre);
        $this->addState('salle_de_bain', $salleDeBain);
        $this->addState('jardin', $jardin);
        $this->addState('garage', $garage);
    }
}
