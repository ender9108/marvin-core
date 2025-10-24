# Marvin - Contexte du Projet Domotique

## Vue d'ensemble

**Marvin** est une solution domotique complète, composée de 3 projets distincts qui communiquent via symfony messenger.

### Stack Technique Globale

- **PHP** : 8.4
- **Symfony** : 7.3
- **API Platform** : 4.2
- **Architecture** : Clean Architecture + DDD + CQRS
- **Message symfony messenger** : PostgreSQL
- **Message broker mqtt** : mosquitto
- **Base de données** : PostgreSQL + TimescaleDB (partagée entre core et manager)
- **Process Manager** : Supervisord
- **Tests** : PHPUnit + Zenstruck Foundry + Zenstruck Browser

## Architecture marvin-core - Structure DDD

### Organisation par Bounded Context

```
src/
├── Security/
│   ├── Application/     (Commands, Queries, CommandHandlers, QueryHandlers, EventHandlers)
│   ├── Domain/          (Entities, ValueObjects, Repositories interfaces, Events, Service)
│   ├── Infrastructure/  (Persistence, Framework)
│   └── Presentation/    (API Resources, Processors, Providers, CLI Commands)
``` 
    

