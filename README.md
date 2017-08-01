Symfony
=======

A Symfony project created on July 6, 2017, 3:28 pm.

Pour tester la platforme, il est nécessaire d'avoir une base de données en fonctionnement. Il faut en configurer l'accès via app/config/parameters.yml :

    database_host: 
    database_port: 
    database_name: 
    database_user: 
    database_password: 
    
Ensuite, il faut démarrer le serveur Symfony : depuis le dossier racine, avec la commande :
    
    php bin/console server:start
    
(php doit être installé)
Puis mettre à jour la base de donnée avec les commandes :
  
    php bin/console doctrine:schema:update --force
    php bin/console doctrine:fixture:load
    
La plateforme est fonctionnelle, consultable en localhost.
