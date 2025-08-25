# EnderLab DDD/CQRS Maker Bundle

Un bundle Symfony (environnement de développement) fournissant des commandes Maker pour accélérer la mise en place d’une architecture DDD/CQRS dans votre projet.

Il permet de :
- Créer la structure d’un domaine (bounded context) en un coup de commande ;
- Générer un modèle de domaine (entité) et l’enrichir de propriétés et relations de manière interactive, avec dépôt (repository) et implémentation Doctrine ;
- Générer une ressource ApiPlatform (DTO) liée à votre modèle et prête à l’emploi avec les State Provider/Processor du bundle EnderLab DddCqrs ApiPlatform.

Ce bundle s’appuie sur Symfony MakerBundle et s’intègre avec :
- EnderLab DddCqrs Bundle (agrégats, événements, buses),
- Doctrine ORM (types/relations),
- ApiPlatform (optionnel),
- EnderLab Timestampable/Blameable (optionnels pour ajouter des traits à la ressource API).

---

## Sommaire
- Installation / Activation (dev)
- Configuration (root_path, excludes)
- Commandes disponibles
  - ddd-cqrs:make:domain
  - ddd-cqrs:make:model
  - ddd-cqrs:make:api-resource
- Structure générée
- Dépendances requises et optionnelles
- Exemples d’utilisation
- Conseils & dépannage

---

## Installation / Activation (dev)

Le bundle est pensé pour l’environnement de développement et est déjà enregistré de cette façon :

config/bundles.php

EnderLab\DddCqrsMakerBundle\DddCqrsMakerBundle::class => ['dev' => true],

Si vous utilisez un autre environnement (par exemple local), veillez à l’activer dans cet environnement.

Assurez-vous d’avoir installé Symfony MakerBundle (dev) :

composer require --dev symfony/maker-bundle

---

## Configuration

Le bundle expose deux options :
- root_path: chemin racine où se trouvent vos domaines (par défaut « %kernel.project_dir%/src »)
- excludes: liste de dossiers à exclure lors du scan (ex : ['Shared', 'Kernel'])

Le paramètre excludes est injecté dans DomainService pour filtrer les domaines et modèles proposés en autocomplétion.

Exemple (config/packages/ddd_cqrs_maker.yaml)

ddd_cqrs_maker:
  root_path: '%kernel.project_dir%/src'
  excludes: ['Shared']

Note : root_path est surtout utile si votre code n’est pas sous src/ ou pour des setups avancés.

---

## Commandes disponibles

### 1) ddd-cqrs:make:domain
Crée un nouveau domaine avec l’arborescence standard DDD et un fichier de configuration de services dédié.

Usage

php bin/console ddd-cqrs:make:domain [domain]

- Si domain est omis, la commande vous le demandera et vérifiera qu’il n’existe pas déjà.
- La commande crée les dossiers suivants sous src/<Domain> :
  - Application/
    - Command/
    - Event/
    - Query/
  - Domain/
    - Event/
    - Exception/
    - Model/
    - Repository/
    - ValueObject/
  - Infrastructure/
    - ApiPlatform/
      - Mapper/
      - Resource/
      - State/
        - Processor/
        - Provider/
    - DataFixtures/
    - Symfony/
      - Repository/

Elle génère aussi un fichier de config dans config/services/<domain_snake_case>.php.

### 2) ddd-cqrs:make:model
Crée/complète un modèle de domaine et génère son dépôt (interface + implémentation Doctrine). Propose en option la génération d’une ressource ApiPlatform.

Usage

php bin/console ddd-cqrs:make:model [domain] [name]

- domain: nom du domaine (ex : System, User, Billing, …)
- name: nom de la classe Modèle (ex : User, Device, Order, …)

Comportement :
- Si le domaine n’existe pas, la commande s’arrête (il faut d’abord créer le domaine).
- Si le modèle n’existe pas, la commande peut vous demander :
  - Le modèle est-il un AggregateRoot (si le bundle DddCqrs est présent) ?
  - Activer Timestampable ?
  - Activer Blameable ?
  - Générer une ApiResource (si ApiPlatform est présent) ?
- La commande ouvre ensuite un dialogue interactif pour ajouter des propriétés :
  - Types scalaires Doctrine (string, text, boolean, integer, float, json, datetime_immutable, etc.)
  - Relations (many_to_one, one_to_many, many_to_many, one_to_one)
    - Avec autocomplétion pour la classe cible (FQCN ou nom court unique)
    - Questions sur le côté propriétaire/inverse, nullable, orphanRemoval, etc.

Fichiers générés/complétés :
- src/<Domain>/Domain/Model/<Name>.php (ajout des propriétés/relations, extends AggregateRoot selon choix)
- src/<Domain>/Domain/Repository/<Name>RepositoryInterface.php
- src/<Domain>/Infrastructure/Doctrine/Repository/Doctrine<Name>Repository.php
- Optionnel : ressource ApiPlatform (voir commande suivante)

### 3) ddd-cqrs:make:api-resource
Génère une classe Ressource ApiPlatform (DTO) pour un modèle existant, s’intégrant avec EnderLab DddCqrs ApiPlatform bundle (provider/processor préconfigurés).

Usage

php bin/console ddd-cqrs:make:api-resource [domain] [name]

- Vérifie que ApiPlatform est disponible, que le domaine et le modèle existent.
- Génère la classe : src/<Domain>/Infrastructure/ApiPlatform/Resource/<Name>Resource.php
  - Utilise des propriétés publiques typées, parsées à partir des champs collectés lors du make:model ou inférés via réflexion si nécessaire
  - Ajoute les traits ResourceTimestampableTrait / ResourceBlameableTrait si ces bundles sont présents et si vous l’avez choisi
  - Configure les opérations Get et GetCollection, un routePrefix basé sur le domaine, et les State Provider/Processor par défaut (EntityToApiStateProvider / ApiToEntityStateProcessor)

---

## Structure générée

Arborescence standard créée par ddd-cqrs:make:domain :

src/<Domain>/
  Application/
    Command/
    Event/
    Query/
  Domain/
    Event/
    Exception/
    Model/
    Repository/
    ValueObject/
  Infrastructure/
    ApiPlatform/
      Mapper/
      Resource/
      State/
        Processor/
        Provider/
    DataFixtures/
    Symfony/
      Repository/

---

## Dépendances

Requises (dev) :
- symfony/maker-bundle

Recommandées / Optionnelles selon fonctionnalités :
- doctrine/orm (pour les types et la génération de relations)
- api-platform/core + api-platform/symfony-bundle (pour la ressource API)
- enderlab/ddd-cqrs-bundle (pour AggregateRoot et l’architecture globale)
- enderlab/ddd-cqrs-api-platform-bundle (provider/processor pour la ressource)
- enderlab/timestampable-bundle, enderlab/blameable-bundle (traits optionnels sur la ressource)

Le bundle suppose une structure d’app par défaut sous src/. Utilisez root_path si nécessaire.

---

## Exemples d’utilisation

1) Créer un domaine « System »

php bin/console ddd-cqrs:make:domain System

2) Créer un modèle User dans le domaine System

php bin/console ddd-cqrs:make:model System User

- Répondre aux questions pour AggregateRoot/Timestampable/Blameable/ApiResource
- Ajouter des propriétés : name (string), email (string), created_at (datetime_immutable), etc.
- Ajouter ensuite une relation : many_to_one vers un autre modèle (par autocomplétion)

3) Générer uniquement la ressource API (si ApiPlatform installé)

php bin/console ddd-cqrs:make:api-resource System User

---

## Conseils & dépannage

- Exclusions : si votre dossier src contient des domaines partagés (ex : Shared, Kernel), ajoutez-les à ddd_cqrs_maker.excludes pour éviter de les proposer dans l’autocomplétion.
- ApiPlatform manquant : la commande ddd-cqrs:make:api-resource affichera un avertissement si ApiPlatform n’est pas installé.
- Modèle existant : si la classe existe déjà, la commande make:model n’écrase pas l’existant mais ajoute des champs/relations via un manipulateur de code (similaire à make:entity).
- Templates : les générateurs s’appuient sur des templates internes (ex : Model.tpl.php, Repository.tpl.php, RepositoryInterface.tpl.php, ApiResource.tpl.php, etc.).
- Relations : en cas de difficulté à résoudre le FQCN de la cible, fournissez le FQCN complet (ex : App\System\Domain\Model\User).
- Permissions/FS : assurez-vous que PHP a les droits d’écriture dans votre projet pour créer les dossiers/fichiers.

---

## Licence

Sous la même licence que le projet. Voir LICENSE à la racine du dépôt.
