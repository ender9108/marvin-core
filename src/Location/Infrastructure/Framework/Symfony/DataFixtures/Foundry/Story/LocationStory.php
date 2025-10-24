<?php

namespace Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story;

use Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\ZoneFactory;
use Zenstruck\Foundry\Story;

class LocationStory extends Story
{
    public function build(): void
    {
        $maison = ZoneFactory::new()
            ->building()
            ->withTemperature(20.5)
            ->withLabel('Maison')
            ->withPath()
            ->create();

        $salon = ZoneFactory::new()
            ->livingRoom()
            ->occupied()
            ->withTemperature(21.0)
            ->withPowerConsumption(450.0)
            ->withLabel('Salon')
            ->withParent($maison->_real())
            ->withPath()
            ->create();

        $cuisine = ZoneFactory::new()
            ->kitchen()
            ->withTemperature(20.0)
            ->withPowerConsumption(800.0)
            ->withLabel('Cuisine')
            ->withParent($maison->_real())
            ->withPath()
            ->create();

        $chambre = ZoneFactory::new()
            ->bedroom()
            ->unoccupied()
            ->withTemperature(19.0)
            ->withPowerConsumption(50.0)
            ->withLabel('Chambre')
            ->withParent($maison->_real())
            ->withPath()
            ->create();

        $salleDeBain = ZoneFactory::new()
            ->bathroom()
            ->withTemperature(22.0)
            ->withPowerConsumption(150.0)
            ->withLabel('Salle de bain')
            ->withParent($maison->_real())
            ->withPath()
            ->create();

        $jardin = ZoneFactory::new()
            ->garden()
            ->withLabel('Jardin')
            ->withParent($maison->_real())
            ->withPath()
            ->create();

        $garage = ZoneFactory::new()
            ->garage()
            ->withTemperature(15.0)
            ->withLabel('Garage')
            ->withParent($maison->_real())
            ->withPath()
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
