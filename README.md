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

o	Mariadb
	MYSQL_ROOT_PASSWORD: 
	MYSQL_DATABASE: 
	MYSQL_USER: 
	MYSQL_PASSWORD: 

o	mariadbtest
	MYSQL_ROOT_PASSWORD: 
	MYSQL_DATABASE: 
	MYSQL_USER: 
	YSQL_PASSWORD: 

o	mongodb
	MONGO_INITDB_ROOT_USERNAME
	MONGO_INITDB_ROOT_PASSWORD: 

-	Dans env.php mettre :
o	DB_USER : root (TEMPORAIREMENT !) Afin d’avoir les droits pour la création de base de données lors de l’initialisation.
o	DB_PASS : le mot de passe root (TEMPORAIREMENT !) Afin d’avoir les droits pour la création de base de données lors de l’initialisation
o	DB_NAME : nom de la base de données mariadb renseignée dans le docker-compose.
o	MONGO_BASE : mongodb://
o	Les id et mot de passe mongodb
o	FROM : Une adresse mail existante sur le serveur de déploiement.
o	SMTPHOST : host smtp
o	SMTPPASS : mot de passe email
o	SMTPPORT : port smtp

-	Dans envTest.php mettre :
o	DB_USER : root (TEMPORAIREMENT !) Afin d’avoir les droits pour la création de base de données lors de l’initialisation.
o	DB_PASS : le mot de passe root (TEMPORAIREMENT !) Afin d’avoir les droits pour la création de base de données lors de l’initialisation
o	DB_NAME : nom de la base de données mariadbTest renseignée dans le docker-compose.
o	FROM : Une adresse mail existante sur le serveur de déploiement.
o	SMTPHOST : host smtp
o	SMTPPASS : mot de passe email
o	SMTPPORT : port smtp

-	Dans phpunit.xml.dist mettre :
o	DB_USER : utilisateur créé dans mariadbTest.
o	DB_PASS : le mot de passe utilisateur créé dans mariadbTest 
o	DB_NAME : nom de la base de données mariadbTest renseignée dans le docker-compose.
o	FROM : Une adresse mail existante sur le serveur de déploiement.
o	SMTPHOST : host smtp
o	SMTPPASS : mot de passe email
o	SMTPPORT : port smtp

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
