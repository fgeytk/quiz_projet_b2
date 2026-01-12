# Quiz PHP - Application Dockerisée

## Prérequis
- Docker Desktop installé et démarré

## Installation et lancement

1. Démarrer Docker Desktop

2. Construire et lancer les conteneurs :
```bash
docker compose up -d
```

3. Attendre que la base de données s'initialise (environ 10-20 secondes)

4. Accéder à l'application :
```
http://localhost:8080
```

## Commandes utiles

- Arrêter les conteneurs :
```bash
docker compose down
```

- Arrêter et supprimer les volumes (reset complet) :
```bash
docker compose down -v
```

- Voir les logs :
```bash
docker compose logs -f
```

- Reconstruire les conteneurs :
```bash
docker compose up -d --build
```

## Configuration

- **Application web** : Port 8080
- **MySQL** : Port 3306
- **Base de données** : quiz
- **Utilisateur MySQL** : quiz
- **Mot de passe MySQL** : quiz
