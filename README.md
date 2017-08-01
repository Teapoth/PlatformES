Symfony
=======

A Symfony project created on July 6, 2017, 3:28 pm.

Pour tester la platforme, il est nécessaire d'avoir une base de données en fonctionnement.

##Installation d'une base de données

Tout d'abord télécharger mysql-server :
    
    sudo apt install mysql-server

et entrer le mot de passe lorsque celui-ci est demandé. Puis démarrer le serveur :

    sudo service mysql start
    
et enfin créer une base de données (en entrant votre mot de passe):

    mysql -u root -p
    CREATE DATABASE Symfony;

##Configuration de Symfony

Il faut en configurer l'accès via app/config/parameters.yml :

    database_host: 127.0.0.1
    database_port: ~
    database_name: Symfony
    database_user: root
    database_password: votre_mot_de_passe
    
Ensuite, il faut démarrer le serveur Symfony : depuis le dossier racine, avec la commande :
    
    php bin/console server:start
    
Remarque : php doit être installé, pour l'installer :

    sudo apt-get php5
    
Puis mettre à jour la base de donnée avec les commandes :
  
    php bin/console doctrine:schema:update --force
    php bin/console doctrine:fixture:load
    
La plateforme est fonctionnelle, consultable en localhost.
