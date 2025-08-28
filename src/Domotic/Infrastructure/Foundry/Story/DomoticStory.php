<?php

namespace App\Domotic\Infrastructure\Foundry\Story;

use App\Domotic\Infrastructure\Foundry\Factory\CapabilityActionFactory;
use App\Domotic\Infrastructure\Foundry\Factory\CapabilityCompositionFactory;
use App\Domotic\Infrastructure\Foundry\Factory\CapabilityFactory;
use App\Domotic\Infrastructure\Foundry\Factory\CapabilityStateFactory;
use App\Domotic\Infrastructure\Foundry\Factory\ProtocolStatusFactory;
use App\Domotic\Infrastructure\Foundry\Factory\ZoneFactory;
use Zenstruck\Foundry\Story;

class DomoticStory extends Story
{
    private array $dataCache = [];

    public function __construct()
    {
        $this->dataCache = [
            'capabilities' => [],
            'capability_actions' => [],
            'capability_states' => [],
        ];
    }

    public function build(): void
    {
        $this->buildZone();
        $this->buildCapabilities();
        $this->buildCapabilityActions();
        $this->buildCapabilityStates();
        $this->buildCapabilityCompositions();
        $this->buildProtocolStatuses();
    }

    private function buildZone(): void
    {
        foreach (ZoneFactory::getDatas() as $data) {
            ZoneFactory::createOne($data);
        }
    }

    private function buildCapabilities(): void
    {
        foreach (CapabilityFactory::getDatas() as $data) {
            $this->dataCache['capabilities'][$data['reference']] = CapabilityFactory::createOne($data);
        }
    }

    private function buildCapabilityActions(): void
    {
        foreach (CapabilityActionFactory::getDatas() as $data) {
            $this->dataCache['capability_actions'][$data['reference']] = CapabilityActionFactory::createOne($data);
        }
    }

    private function buildCapabilityStates(): void
    {
        foreach (CapabilityStateFactory::getDatas() as $data) {
            $this->dataCache['capability_states'][$data['reference']] = CapabilityStateFactory::createOne($data);
        }
    }

    private function buildCapabilityCompositions(): void
    {
        foreach (CapabilityCompositionFactory::getDatas() as $data) {
            $data = array_merge($data, [
                'capability' => $this->dataCache['capabilities'][$data['capability']],
            ]);

            $actions = $data['capabilityActions'];
            $data['capabilityActions'] = [];

            foreach ($actions as $actionReference) {
                $data['capabilityActions'][] = $this->dataCache['capability_actions'][$actionReference];
            }

            $states = $data['capabilityStates'];
            $data['capabilityStates'] = [];

            foreach ($states as $stateReference) {
                $data['capabilityStates'][] = $this->dataCache['capability_states'][$stateReference];
            }

            CapabilityCompositionFactory::createOne($data);
        }
    }

    private function buildProtocolStatuses(): void
    {
        foreach (ProtocolStatusFactory::getDatas() as $data) {
            ProtocolStatusFactory::createOne($data);
        }
    }
}
