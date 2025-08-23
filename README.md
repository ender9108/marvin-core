# Marvin core

```mermaid
flowchart TB

  %% Devices
  zigbee[Device Zigbee]
  matter[Device Matter]
  zwave[Device Z-Wave]

  %% MQTT broker
  mqtt[(Broker MQTT)]

  %% Workers ingest (device -> core)
  wZigbee[Worker Zigbee (Symfony)]
  wMatter[Worker Matter (Node.js)]
  wZwave[Worker Z-Wave (Symfony/Node.js)]

  %% Core
  core[Marvin Core (Symfony)]

  %% Exchanges & queues
  subgraph RabbitMQ
    evQ[(Queue domotic.events)]
    cmdEx{Exchange domotic.commands (headers)}
    domoticDLX{Exchange domotic.dlx (fanout)}

    zigQ[(Queue zigbee.commands)]
    matQ[(Queue matter.commands)]
    zwaQ[(Queue zwave.commands)]

    zigDLQ[(Queue zigbee.commands.dlq)]
    matDLQ[(Queue matter.commands.dlq)]
    zwaDLQ[(Queue zwave.commands.dlq)]
  end

  %% Device -> MQTT
  zigbee -->|zigbee2mqtt/#| mqtt
  matter -->|matter2mqtt/#| mqtt
  zwave  -->|zwave2mqtt/#| mqtt

  %% MQTT -> Workers ingest
  mqtt --> wZigbee
  mqtt --> wMatter
  mqtt --> wZwave

  %% Workers ingest -> AMQP
  wZigbee --> evQ
  wMatter --> evQ
  wZwave  --> evQ

  %% Core consume events
  evQ --> core

  %% Core send command -> exchange headers
  core -->|DomoticCommand (protocol header)| cmdEx

  %% Exchange headers routing
  cmdEx -->|protocol=zigbee| zigQ
  cmdEx -->|protocol=matter| matQ
  cmdEx -->|protocol=zwave| zwaQ

  %% Workers consume commands -> MQTT
  zigQ --> wZigbee
  matQ --> wMatter
  zwaQ --> wZwave

  %% Workers publish to devices
  wZigbee -->|zigbee2mqtt/<device>/set| mqtt
  wMatter -->|matter2mqtt/<device>/set| mqtt
  wZwave  -->|zwave2mqtt/<device>/set| mqtt

  %% Dead Letter routing
  zigQ -.x-dead-letter-exchange.-> domoticDLX
  matQ -.x-dead-letter-exchange.-> domoticDLX
  zwaQ -.x-dead-letter-exchange.-> domoticDLX

  domoticDLX --> zigDLQ
  domoticDLX --> matDLQ
  domoticDLX --> zwaDLQ
```
