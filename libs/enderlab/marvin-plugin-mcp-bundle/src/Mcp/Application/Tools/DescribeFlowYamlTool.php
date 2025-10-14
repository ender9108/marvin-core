<?php

namespace EnderLab\MarvinPluginMcpBundle\Mcp\Application\Tools;

use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'describe-flow-yaml',
    description: 'Décrit la syntaxe YAML complète utilisée pour définir un Flow Marvin.',
)]
class DescribeFlowYamlTool
{
    public function __invoke(): array
    {
        $description = <<<YAML
# Syntaxe YAML de définition d’un Flow Marvin
#
# Chaque flow décrit un scénario domotique sous forme déclarative.
# Un flow contient :
#   - un ou plusieurs déclencheurs ("triggers")
#   - des conditions optionnelles ("conditions")
#   - une ou plusieurs actions ("actions")

flow:
  name: "Fermer les volets la nuit"
  description: "Ferme tous les volets au coucher du soleil."
  enabled: true

  triggers:
    - type: time
      at: "22:00"
    - type: sun
      event: "sunset"

  conditions:
    - type: weather
      key: "rain"
      operator: "not_equals"
      value: true
    - type: state
      device: "presence.bureau"
      equals: false

  actions:
    - type: device
      device: "volets.salon"
      command: "close"
    - type: delay
      seconds: 10
    - type: device
      device: "volets.chambre"
      command: "close"

# Champs supportés
flow:
  name: string
  description: string?
  enabled: boolean
  triggers: Trigger[]
  conditions: Condition[]
  actions: Action[]

Trigger:
  type: string (time | sun | weather | state | mqtt | event)
  at?: string (ex: "22:00")
  event?: string ("sunrise" | "sunset")
  topic?: string
  key?: string
  operator?: string
  value?: mixed

Condition:
  type: string (state | weather | numeric | custom)
  device?: string
  key?: string
  operator?: string ("equals" | "not_equals" | "gt" | "lt" | "in" | "not_in")
  value?: mixed

Action:
  type: string (device | flow | http | mqtt | delay | script)
  device?: string
  command?: string
  data?: mixed
  flow?: string
  url?: string
  method?: string
  body?: mixed
  seconds?: int

# Exemple minimal
flow:
  name: "Allumer la lumière quand mouvement"
  triggers:
    - type: state
      device: "motion.salon"
      equals: true
  actions:
    - type: device
      device: "light.salon"
      command: "on"
YAML;

        return [
            'syntax' => $description,
            'format' => 'yaml',
            'purpose' => 'Aide les IA à générer du YAML conforme pour les Flows Marvin.'
        ];
    }
}
