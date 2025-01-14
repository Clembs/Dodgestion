# Mini-projet R3.01

Ce site web permet de gérer une équipe sportive. Projet scolaire du BUT Informatique dans le but de maîtriser PHP.

## Commandes

Lancer le serveur PHP pour ouvrir le projet :

```
php -S localhost:[port] -t public/
```

## Structure du projet

- `public/`: Dossier contenant les fichiers accessibles publiquement
- `public/index.php`: Routeur du projet, accède aux différentes vues
- `public/globals.css`: Feuilles de style globales
- `src/`: Dossier contenant les fichiers PHP
- `src/controllers/`: Dossier contenant les contrôleurs
- `src/models/`: Dossier contenant les modèles
- `src/views/`: Dossier contenant les vues

## Configuration

Le fichier `php.ini` doit contenir les informations suivantes pour que la base de données fonctionne :

```ini
[database]
database_url = {url d'une base de données postgresql}
```
