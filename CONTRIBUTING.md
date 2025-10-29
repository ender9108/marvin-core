# Guide de Contribution

Merci de ton intérêt pour contribuer à ce projet ! 🎉

## 📜 Licence des contributions

En contribuant à ce projet, tu acceptes que tes contributions soient distribuées sous la même licence **GNU Affero General Public License v3.0 (AGPL-3.0)**.

### Ce que cela signifie :

- Tes contributions deviendront open source sous AGPL v3
- Elles pourront être utilisées, modifiées et redistribuées par d'autres
- Tu conserves le copyright de ton code
- Tu accordes au projet et à tous les utilisateurs une licence irrévocable pour utiliser ta contribution

## 🔧 Comment contribuer

### 1. Fork et Clone

```bash
# Fork le projet sur GitHub/GitLab
git clone https://github.com/ender9108/marvin-core.git
cd [marvin-core]
```

### 2. Crée une branche

```bash
git checkout -b feature/ma-super-fonctionnalite
```

### 3. Développe et teste

- Écris du code propre et testé
- Respecte les conventions du projet (PSR-12, architecture DDD/CQRS)
- Ajoute des tests unitaires/fonctionnels si nécessaire
- Assure-toi que tous les tests passent

```bash
composer test
composer phpstan
composer cs-check
```

### 4. Commit avec un message clair

```bash
git commit -m "feat: Ajout du support pour Matter protocol"
```

Convention des messages de commit :
- `feat:` nouvelle fonctionnalité
- `fix:` correction de bug
- `docs:` documentation
- `refactor:` refactoring sans changement de comportement
- `test:` ajout/modification de tests
- `chore:` tâches de maintenance

### 5. Pousse et crée une Pull Request

```bash
git push origin feature/ma-super-fonctionnalite
```

Puis crée une Pull Request sur GitHub/GitLab avec :
- Description claire de ce que fait ta contribution
- Référence aux issues liées (si applicable)
- Screenshots/logs si pertinent

## 📋 Checklist avant de soumettre

- [ ] Mon code respecte les standards du projet
- [ ] J'ai ajouté des tests pour ma fonctionnalité
- [ ] Tous les tests passent
- [ ] J'ai mis à jour la documentation si nécessaire
- [ ] Mon commit message est clair et descriptif
- [ ] J'ai ajouté l'en-tête de licence AGPL dans les nouveaux fichiers

## 📝 En-tête de licence dans les fichiers

Chaque nouveau fichier PHP doit contenir cet en-tête :

```php
<?php

/*
 * Copyright (C) 2025 Alexandre Berthelot (github: ender9108)
 *
 * This file is part of marvin-core.
 *
 * marvin-core is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * marvin-core is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with marvin-core. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace VotreNamespace;

// Ton code ici
```

## 🤝 Code de Conduite

- Sois respectueux et constructif
- Accueille les nouvelles contributions avec bienveillance
- Fournis des retours constructifs dans les reviews
- Accepte les critiques constructives sur ton code

## ❓ Questions ?

Si tu as des questions sur :
- Comment contribuer : ouvre une issue avec le label `question`
- La licence AGPL v3 : consulte [LICENSING.md](./LICENSING.md)
- L'architecture du projet : consulte la documentation dans `/docs`

## 🙏 Merci !

Chaque contribution, qu'elle soit grande ou petite, est précieuse pour faire avancer ce projet open source !
