<?php

namespace Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story;

use Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\ZoneFactory;
use ReflectionException;
use Zenstruck\Foundry\Story;

final class LocationStory extends Story
{
    /**
     * @throws ReflectionException
     */
    public function build(): void
    {
        // Niveau 0 : Maison (root)
        $maison = ZoneFactory::new()
            ->building('Ma Maison')
            ->create();

        // Niveau 1 : Étages
        $rdc = ZoneFactory::new()
            ->floor('Rez-de-chaussée')
            ->withParent($maison->_real())
            ->create();

        $etage1 = ZoneFactory::new()
            ->floor('Étage 1')
            ->withParent($maison->_real())
            ->create();

        $exterieur = ZoneFactory::new()
            ->floor('Extérieur')
            ->withParent($maison->_real())
            ->create();

        // Niveau 2 : Pièces du RDC
        $salon = ZoneFactory::new()
            ->livingRoom()
            ->withParent($rdc->_real())
            ->withTemperature(21.5)
            ->withHumidity(50.0)
            ->withPowerConsumption(450.0)
            ->withActiveSensors(2)
            ->occupied()
            ->create();

        $cuisine = ZoneFactory::new()
            ->kitchen()
            ->withParent($rdc->_real())
            ->withTemperature(20.5)
            ->withHumidity(55.0)
            ->withPowerConsumption(800.0)
            ->withActiveSensors(1)
            ->unoccupied()
            ->create();

        $salledebain = ZoneFactory::new()
            ->bathroom()
            ->withParent($rdc->_real())
            ->withTemperature(22.0)
            ->withHumidity(70.0)
            ->withActiveSensors(1)
            ->unoccupied()
            ->create();

        // Niveau 2 : Pièces de l'étage 1
        $chambre1 = ZoneFactory::new()
            ->bedroom(1)
            ->withParent($etage1->_real())
            ->withTemperature(19.0)
            ->withHumidity(45.0)
            ->withActiveSensors(1)
            ->unoccupied()
            ->create();

        $chambre2 = ZoneFactory::new()
            ->bedroom(2)
            ->withParent($etage1->_real())
            ->withTemperature(18.5)
            ->withHumidity(48.0)
            ->withActiveSensors(1)
            ->occupied()
            ->create();

        $bureau = ZoneFactory::new()
            ->office()
            ->withParent($etage1->_real())
            ->withTemperature(20.0)
            ->withHumidity(42.0)
            ->withPowerConsumption(200.0)
            ->withActiveSensors(1)
            ->occupied()
            ->create();

        // Niveau 2 : Zones extérieures
        $jardin = ZoneFactory::new()
            ->garden()
            ->withParent($exterieur->_real())
            ->create();

        $garage = ZoneFactory::new()
            ->garage()
            ->withParent($exterieur->_real())
            ->withPowerConsumption(100.0)
            ->create();

        // Enregistrer les zones dans le pool pour y accéder facilement
        $this->addState('maison', $maison->_real());
        $this->addState('rdc', $rdc->_real());
        $this->addState('etage1', $etage1->_real());
        $this->addState('exterieur', $exterieur->_real());
        $this->addState('salon', $salon->_real());
        $this->addState('cuisine', $cuisine->_real());
        $this->addState('salledebain', $salledebain->_real());
        $this->addState('chambre1', $chambre1->_real());
        $this->addState('chambre2', $chambre2->_real());
        $this->addState('bureau', $bureau->_real());
        $this->addState('jardin', $jardin->_real());
        $this->addState('garage', $garage->_real());
    }
}
