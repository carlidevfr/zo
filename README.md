Auteur : Anthony ABBES
Licence : GNU

Déploiement sur Docker :

-	Installer Docker
-	Installer Git
-	Créer un dossier pour accueillir le site
-	Ouvrir le terminal et se positionner dans ce dossier
-	« git clone https://github.com/carlidevfr/zo.git . »
-	Renommer Dockerfile.dev en Dockerfile
-	Renommer docker-compose.yml.dev en docker-compose.yml
-	Renommer phpunit.xml.dist.dev en phpunit.xml.dist
-	Renommer dans src/config env.php.dev en env.php
-	Renommer dans src/config envTest.php.dev en envTest.php
-	Renommer dans src/data prod.sql.dev en prod.sql
-	Renommer dans src/test/data prod.sql.dev testprod.sql

-	Dans le docker-compose renseigner des noms, id et mot de passe forts pour :

    	Mariadb
    	MYSQL_ROOT_PASSWORD: 
    	MYSQL_DATABASE: 
    	MYSQL_USER: 
    	MYSQL_PASSWORD: 

    	mariadbtest
    	MYSQL_ROOT_PASSWORD: 
    	MYSQL_DATABASE: 
    	MYSQL_USER: 
    	YSQL_PASSWORD: 

    	mongodb
    	MONGO_INITDB_ROOT_USERNAME
    	MONGO_INITDB_ROOT_PASSWORD: 

-	Dans env.php mettre :
    	DB_USER : root (TEMPORAIREMENT !) Afin d’avoir les droits pour la création de base de données lors de l’initialisation.
    	DB_PASS : le mot de passe root (TEMPORAIREMENT !) Afin d’avoir les droits pour la création de base de données lors de l’initialisation
    	DB_NAME : nom de la base de données mariadb renseignée dans le docker-compose.
    	MONGO_BASE : mongodb://
    	Les id et mot de passe mongodb
    	FROM : Une adresse mail existante sur le serveur de déploiement.
    	SMTPHOST : host smtp
    	SMTPPASS : mot de passe email
    	SMTPPORT : port smtp

-	Dans envTest.php mettre :
    	DB_USER : root (TEMPORAIREMENT !) Afin d’avoir les droits pour la création de base de données lors de l’initialisation.
    	DB_PASS : le mot de passe root (TEMPORAIREMENT !) Afin d’avoir les droits pour la création de base de données lors de l’initialisation
    	DB_NAME : nom de la base de données mariadbTest renseignée dans le docker-compose.
    	FROM : Une adresse mail existante sur le serveur de déploiement.
    	SMTPHOST : host smtp
    	SMTPPASS : mot de passe email
    	SMTPPORT : port smtp

-	Dans phpunit.xml.dist mettre :
    	DB_USER : utilisateur créé dans mariadbTest.
    	DB_PASS : le mot de passe utilisateur créé dans mariadbTest 
    	DB_NAME : nom de la base de données mariadbTest renseignée dans le docker-compose.
    	FROM : Une adresse mail existante sur le serveur de déploiement.
    	SMTPHOST : host smtp
    	SMTPPASS : mot de passe email
    	SMTPPORT : port smtp

-	Dans le fichier prod.sql mettre aux lignes 3,4 et 5 le nom de la base de données saisi sous mariadb dans le fichier docker

-	Dans le fichier testprod.sql mettre aux lignes 3,4 et 5 le nom de la base de données saisi sous mariadbtest dans le fichier docker

-	Dans index.php décommenter les lignes 35, 48 et 49
Nb : L'envoi d'e-mails via Gmail à l'aide de PHPMailer est devenu un peu plus complexe : 
•	Accédez aux paramètres de votre compte Google.
•	Accédez à Sécurité > Connexion à Google > Mots de passe des applications.
•	Saisissez un nom, google génèrera un mot de passe qui sera à utiliser (sans les espaces) dans l’application.

-	Ouvrez un terminal
o	Se placer dans le dossier de l’application
o	Faire la commande docker-compose up -d –build
o	Faire la commande docker ps pour identifier le container php
o	Faire la commande docker exec -it -u root agency-php-1 /bin/bash (remplacer agency-php-1 par le nom de votre container qui utilise php)
o	Si vous ne souhaitez pas utiliser phpunit ni faire de tests : composer install --prefer-source --no-dev
o	Sinon : composer install --prefer-source
o	Pour mettre à jour votre code vous pourrez ici faire des git pull

-	Accédez à l’url localhost
-	Si la page s’affiche aller dans ./createbddprod et ./createbddtest pour initialiser les bases de données
-	Dans env.php et envtest.php ôter l’identifiant mot de passe root et mettre ceux des utilisateurs créés dans le docker-compose
-	Les erreurs sont consultables dans error.log sous src.
-	Pour lancer les tests tapez la commande vendor/bin/phpunit.


Déploiement sur PlanetHoster :

-	Commander un compte d’hébergement et un nom de domaine
-	S’y connecter
-	Créer un dossier pour accueillir le site
-	Domaine -> Gestion des domaines -> SSL TLS -> Renouveller. Afin de commander un certificat SSL. Peut nécessiter quelques heures.
-	Langage -> Php -> Extensions Php -> Activer MongoDb
-	Fichiers -> Terminal -> taper la commande : cd www 
-	Taper la commande : git clone https://github.com/carlidevfr/zo.git . 
-	Commande :  cd .. 
-	Commande : php -r "readfile('https://getcomposer.org/installer');" > composer.php
-	Commande : php composer.php
-	Commande : nano .bash_profile
-	Ajouter cette ligne : alias composer="php /home/user/composer.phar"
-	Enregistrer et quitter (clavier ctrl + action)
-	Commande : source ~/.bash_profile (pour utiliser composer dans la console, il faudra faire cette dernière commande à chaque nouvelle ouverture du terminal)
o	Commande :  cd .. 
o	Commande : cd www
o	Si vous ne souhaitez pas utiliser phpunit ni faire de tests : composer install --prefer-source --no-dev
o	Sinon : composer install --prefer-source
o	En cas de problème de version mongodb empêchant l’installation faire : composer require mongodb/mongodb puis retaper la commande précédente
o	Pour mettre à jour votre code vous pourrez ici (dans le fichier www) faire des git pull
-	Base de données -> Bases de données SQL créer une bdd pour la prod et une bdd test.
-	Créer un utilisateur pour chacune de ces bases.
-	ATTENTION. Pour initialiser les bdd leur donner tous les droits. PUIS ensuite modifier pour autoriser ‘SELECT, INSERT, UPDATE, DELETE’ uniquement.
-	Si vous souhaitez créer les utilisateurs via phpmyadmin, prendre les lignes commentées en fin de fichier prod.sql en modifiant le nom de la base de données et en mettant le mot de passe haché en sha1.
-	Pour Mongodb, créer un compte sur mongodb Atlas.
-	Remplir le formulaire
-	Choisir l’option gratuite M0
-	Create Deployment
-	Récupérer les informations de connexion et faire ‘create database user’
-	Ajouter l’ip publique du serveur (domaine->gestion des domaines -> dans le champ A.)
-	Choose a connection Method
-	Driver -> Php-> Copier tout ce qui est après l’@ (@ exclus), c’est le host.
-	Done

-	Renommer phpunit.xml.dist.dev en phpunit.xml.dist
-	Renommer dans src/config env.php.dev en env.php
-	Renommer dans src/config envTest.php.dev en envTest.php
-	Renommer dans src/data prod.sql.dev en prod.sql
-	Renommer dans src/test/data prod.sql.dev testprod.sql
-	Dans env.php mettre :
o	DB_HOST : localhost
o	DB_USER : l’utilisateur de la bddprod (attention, un préfixe est ajouté lors de la création de l’user).
o	DB_PASS : le mot de passe
o	DB_NAME : nom de la base de données de production
o	MONGO_BASE : mongodb+srv://
o	Les id et mot de passe mongodb
o	MONGO_INITDB_HOST : le host (tout après l’@ de la copie précédente)
o	FROM : Une adresse mail existante sur le serveur de déploiement.
o	SMTPHOST : host smtp
o	SMTPPASS : mot de passe email
o	SMTPPORT : port smtp

-	Dans envTest.php mettre :
o	DB_HOST : localhost
o	DB_USER : l’utilisateur de la bddtest (attention, un préfixe est ajouté lors de la création de l’user).
o	DB_PASS : le mot de passe
o	DB_NAME : nom de la base de données de test
o	FROM : Une adresse mail existante sur le serveur de déploiement.
o	SMTPHOST : host smtp
o	SMTPPASS : mot de passe email
o	SMTPPORT : port smtp

-	Dans phpunit.xml.dist mettre :
o	DB_HOST : localhost
o	DB_USER : l’utilisateur de la bddtest (attention, un préfixe est ajouté lors de la création de l’user).
o	DB_PASS : le mot de passe
o	DB_NAME : nom de la base de données de test
o	FROM : Une adresse mail existante sur le serveur de déploiement.
o	SMTPHOST : host smtp
o	SMTPPASS : mot de passe email
o	SMTPPORT : port smtp

-	Dans le fichier prod.sql mettre aux lignes 3,4 et 5 le nom de la base de données saisie

-	Dans le fichier testprod.sql mettre aux lignes 3,4 et 5 le nom de la base de données saisi sous mariadbtest dans le fichier docker

-	Dans index.php décommenter les lignes 35, 48 et 49
Nb : L'envoi d'e-mails via Gmail à l'aide de PHPMailer est devenu un peu plus complexe : 
•	Accédez aux paramètres de votre compte Google.
•	Accédez à Sécurité > Connexion à Google > Mots de passe des applications.
•	Saisissez un nom, google génèrera un mot de passe qui sera à utiliser (sans les espaces) dans l’application.

-	Accédez à l’url du site
-	Si la page s’affiche aller dans ./createbddprod et ./createbddtest pour initialiser les bases de données (en cas d’erreur consulter error.log)
-	Dans index.php re-commenter les lignes précédentes
-	Les erreurs sont consultables dans error.log sous src.
-	Pour lancer les tests tapez la commande vendor/bin/phpunit.
-	Dans base de données->base de données sql mettre les droits SELECT, INSERT, UPDATE, DELETE’ uniquement.
