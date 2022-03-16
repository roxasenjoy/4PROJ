# PROJET DE FIN D'ANNÉE

Projet de fin de 4eme année : Refaire campus booster

## Environnement de développement

### Lien de téléchargement pour Symfony CLI

    *Télécharger goFish : https://gofi.sh/#install (Attention de bien se mettre dans le Powershell en admin)
    *Télécharger Symfony CLI : https://symfony.com/download 

### Pré-requis

    * PHP 8.0.10
    * Composer 2.2.7
    * Symfony CLI 5.4.2
    * Docker
    * docker-compose
    * yarn 1.22.17
    * npm 7.24.1
    
### Mise en place du projet

Maintenant que vous avez toutes les versions, nous pouvons commencer à initaliser le projet.

Effectuer les commandes suivantes : 

    * git clone https://github.com/roxasenjoy/4PROJ.git
    * composer install
    * composer require symfony/webpack-encore-bundle
    * yarn run build

### Lancer l'environnement de développement
```bash
docker-compose up -d
symfony serve -d
```


#### TIPS

Afin d'éviter de devoir faire `yarn run build`, vous avez la possibilité de faire `yarn run watch` afin que votre enregistrement fonctionne comme-ci on faisait la commande `yarn run build`