# [NOM DE TON PROJET] 🏠🤖

> Système domotique open source basé sur DDD/CQRS avec support multi-protocoles (Zigbee, Matter, Thread, Z-Wave, WiFi)

[![License: AGPL v3](https://img.shields.io/badge/License-AGPL_v3-blue.svg)](https://www.gnu.org/licenses/agpl-3.0)
[![PHP Version](https://img.shields.io/badge/PHP-8.4-purple.svg)](https://www.php.net/)
[![Symfony](https://img.shields.io/badge/Symfony-7.3-black.svg)](https://symfony.com/)
[![API Platform](https://img.shields.io/badge/API_Platform-4.2-blue.svg)](https://api-platform.com/)

## 📋 Description

[TON PROJET] est un système de gestion domotique moderne et évolutif, conçu avec une architecture clean (DDD/CQRS) et supportant les principaux protocoles domotiques du marché.

### 🎯 Objectifs

- Architecture propre et maintenable (Clean Architecture, DDD, CQRS)
- Support multi-protocoles domotiques
- Scalabilité et performance (Messaging, TimescaleDB)
- Open source et communautaire

## 🚀 Fonctionnalités

- ✅ **Multi-protocoles** : Zigbee (via zigbee2mqtt), WiFi, Matter*, Thread*, Z-Wave*
- ✅ **Architecture moderne** : DDD, CQRS, Event Sourcing
- ✅ **API robuste** : API Platform 4.2 + Symfony 7.3
- ✅ **Messaging asynchrone** : PostgreSQL pour la communication inter-services
- ✅ **Base de données optimisée** : TimescaleDB pour les séries temporelles
- ✅ **Supervision** : Supervisord pour la gestion des processus

*En cours de développement

## 🛠️ Stack Technique

- **Backend** : PHP 8.4, Symfony 7.3
- **API** : API Platform 4.2
- **Message Broker** : PostgreSQL
- **Base de données** : PostgreSQL + TimescaleDB
- **Process Manager** : Supervisord
- **Architecture** : Clean Architecture, DDD, CQRS

## 📦 Installation

```bash
# Clone le projet
git clone https://github.com/ender9108/marvin-core.git
cd marvin-core

# Installe les dépendances
composer install

# Configure l'environnement
cp .env .env.local
# Édite .env.local avec tes paramètres

# Lance les conteneurs Docker
docker-compose up -d

# Crée la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Lance les workers RabbitMQ
supervisord -c supervisord.conf
```

## 📖 Documentation

- [Architecture](/docs/architecture.md)
- [Protocoles supportés](/docs/protocols.md)
- [Guide de contribution](CONTRIBUTING.md)
- [Informations de licence](LICENSING.md)

## 🤝 Contribuer

Les contributions sont les bienvenues ! Consulte [CONTRIBUTING.md](CONTRIBUTING.md) pour plus d'informations.

## 📜 Licence

Ce projet est distribué sous licence **GNU Affero General Public License v3.0 (AGPL-3.0)**.

### En bref :

- ✅ Utilisation libre (personnelle et commerciale)
- ✅ Modification et redistribution autorisées
- ⚠️ **Obligation de partager le code source des modifications** (même en déploiement réseau/SaaS)
- ⚠️ Toute version dérivée doit rester sous AGPL v3

Pour plus de détails, consulte :
- [LICENSE](LICENSE) - Texte de la licence
- [LICENSING.md](LICENSING.md) - Explications détaillées

### 💼 Licence Commerciale

Une licence commerciale propriétaire est disponible si tu souhaites utiliser ce logiciel sans les obligations de l'AGPL v3 (par exemple, dans un produit propriétaire ou un SaaS fermé).

**Contact** : alexandreberthelot9108@gmail.com

## 🙏 Remerciements

Merci à tous les contributeurs qui font vivre ce projet open source !

## 📧 Contact

- **Auteur** : Alexandre Berthelot
- **Email** : alexandreberthelot9108@gmail.com
- **Website** : 
- **GitHub** : https://github.com/ender9108

---

**Note** : Ce projet est en développement actif. N'hésite pas à ouvrir des issues ou des pull requests !
