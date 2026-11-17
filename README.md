# convertisseur-test-php

## 1. Objectif de l'exercice

Créer un **outil en ligne de commande** en PHP qui convertit un fichier d'un format vers un autre, puis une petite **interface web** pour l'utiliser plus simplement.

## 2. Structure du projet (état actuel)

Chemin : `~/Projets/Docs/Live_Campus2/Cours_Live_Campus2/05_PHP/exercices/5-exo/convertisseur/`

```
convertisseur/
├── .gitignore
├── composer.json
├── composer.lock
├── console
├── index.php
├── mauvais.json       ← à supprimer (fichier de test)
├── resultat.xml       ← à supprimer (fichier de test)
├── src/
│   └── ConvertirCommand.php
├── test.csv
├── test.json
└── vendor/
```

## 3. Comment ça fonctionne (à retenir pour l'oral)

```
NAVIGATEUR                  index.php                    console
    │                           │                           │
    │── fichier + format ──────►│                           │
    │                           │── copie dans /tmp         │
    │                           │── php console … ─────────►│ lit, convertit
    │                           │◄── texte converti ────────│
    │                           │── supprime la copie /tmp  │
    │◄── page avec résultat ────│                           │
```

| Élément              | Rôle                                           |
| -------------------- | ---------------------------------------------- |
| `$_FILES`            | Contient le fichier envoyé par le formulaire   |
| `sys_get_temp_dir()` | Dossier temporaire du système (`/tmp`)         |
| `uniqid()`           | Nom unique : évite les collisions              |
| `shell_exec()`       | Lance la commande `console` depuis PHP         |
| `escapeshellarg()`   | Sécurise l'argument passé au terminal          |
| `2>&1`               | Redirige les erreurs vers le résultat affiché  |
| `htmlspecialchars()` | Empêche le XML d'être interprété comme du HTML |
| `unlink()`           | Supprime la copie temporaire                   |
