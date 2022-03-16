# PROJET DE FIN D'ANNÉE

Projet de fin de 4eme année : Refaire campus booster

## Environnement de développement

### Lien de téléchargement pour Symfony CLI

    *Télécharger goFish : https://gofi.sh/#install (Attention de bien se mettre dans le Powershell en admin)
    *Télécharger Symfony CLI : https://symfony.com/download 

### Pré-requis

    * PHP 7.2.5
    * Composer
    * Symfony CLI
    * Docker
    * docker-compose
    

Vous pouvez vérifier les pré-requis (sauf Docker et docker-compose) avec la commande suivante (de la CLI Symfony) : 
```bash
symfony check:requirements
```

### Lancer l'environnement de développement
```bash
docker-compose up -d
symfony serve -d
```