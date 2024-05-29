<?php

// Sécurise le cookie de session avec httponly
session_set_cookie_params([
    'lifetime' => 600, // 10 minutes d'inactivité max
    'path' => '/',
    'domain' => $_SERVER['SERVER_NAME'],
    'httponly' => true,
    'samesite' => 'Strict', // Définir le SameSite sur Strict pour plus de sécurité
    //'secure' => true // Indique que le cookie ne doit être envoyé que via une connexion HTTPS
]);

session_start();

require_once './vendor/autoload.php';
define("BASE_URL", '/');

// inclusion des classes
require_once './src/Model/Common/Router.php';
require_once './src/Config/env.php';
require_once './src/Controller/HomeController.php';
require_once './src/Controller/ContactController.php';
require_once './src/Controller/UtilisateurHomeController.php';
require_once './src/Controller/UtilisateurRaceController.php';
require_once './src/Controller/UtilisateurAvisController.php';
require_once './src/Controller/UtilisateurHabitatController.php';
require_once './src/Controller/UtilisateurAnimauxController.php';
require_once './src/Controller/UtilisateurServiceController.php';
require_once './src/Controller/UtilisateurHabitatVeteController.php';
require_once './src/Controller/UtilisateurRapportVeteController.php';
require_once './src/Controller/ConsommationNourritureController.php';
require_once './src/Controller/UtilisateurCompteController.php';




require_once './src/Model/Common/Security.php';
require_once './src/Model/Common/Regenerate.php';


$router = new Router();

$router->addRoute('GET', BASE_URL . '/', 'homecontroller', 'index');
$router->addRoute('GET', BASE_URL . 'nos-services', 'homecontroller', 'servicePage');
$router->addRoute('GET', BASE_URL . 'nos-habitats', 'homecontroller', 'habitatsPage');
$router->addRoute('GET', BASE_URL . 'nos-habitats/habitat', 'homecontroller', 'habitatByIdPage');
$router->addRoute('GET', BASE_URL . 'animal', 'homecontroller', 'animalByIdPage');
$router->addRoute('GET', BASE_URL . 'contact', 'ContactController', 'contactPage');
$router->addRoute('POST', BASE_URL . 'contact', 'ContactController', 'contactForm');





$router->addRoute('GET', BASE_URL . 'createbddprod', 'homecontroller', 'createBddProd');
$router->addRoute('GET', BASE_URL . 'createbddtest', 'homecontroller', 'createBddTest');

$router->addRoute('GET', BASE_URL . 'admin', 'UtilisateurHomeController', 'utilisateurHomePage');
$router->addRoute('GET', BASE_URL . 'login', 'UtilisateurHomeController', 'userLogin');
$router->addRoute('POST', BASE_URL . 'login', 'UtilisateurHomeController', 'userLogin');
$router->addRoute('GET', BASE_URL . 'logout', 'UtilisateurHomeController', 'userLogout');

$router->addRoute('GET', BASE_URL . 'admin/manage-race', 'UtilisateurRaceController', 'adminRacePage');
$router->addRoute('POST', BASE_URL . 'admin/manage-race/add', 'UtilisateurRaceController', 'adminAddRace');
$router->addRoute('GET', BASE_URL . 'admin/manage-race/action/success', 'UtilisateurRaceController', 'adminSuccessActionRace');
$router->addRoute('POST', BASE_URL . 'admin/manage-race/delete', 'UtilisateurRaceController', 'adminDeleteRace');
$router->addRoute('GET', BASE_URL . 'admin/manage-race/update', 'UtilisateurRaceController', 'adminUpdateRacePage');
$router->addRoute('POST', BASE_URL . 'admin/manage-race/update', 'UtilisateurRaceController', 'adminUpdateRace');

$router->addRoute('GET', BASE_URL . 'admin/manage-service', 'UtilisateurServiceController', 'adminServicePage');
$router->addRoute('POST', BASE_URL . 'admin/manage-service/add', 'UtilisateurServiceController', 'adminAddService');
$router->addRoute('GET', BASE_URL . 'admin/manage-service/action/success', 'UtilisateurServiceController', 'adminSuccessActionService');
$router->addRoute('POST', BASE_URL . 'admin/manage-service/delete', 'UtilisateurServiceController', 'adminDeleteService');
$router->addRoute('GET', BASE_URL . 'admin/manage-service/update', 'UtilisateurServiceController', 'adminUpdateServicePage');
$router->addRoute('POST', BASE_URL . 'admin/manage-service/update', 'UtilisateurServiceController', 'adminUpdateService');

$router->addRoute('GET', BASE_URL . 'admin/manage-avis', 'UtilisateurAvisController', 'adminAvisPage');
$router->addRoute('GET', BASE_URL . 'admin/manage-avis/action/success', 'UtilisateurAvisController', 'adminSuccessActionAvis');
$router->addRoute('POST', BASE_URL . 'admin/manage-avis/delete', 'UtilisateurAvisController', 'adminDeleteAvis');
$router->addRoute('GET', BASE_URL . 'admin/manage-avis/update', 'UtilisateurAvisController', 'adminUpdateAvisPage');
$router->addRoute('POST', BASE_URL . 'admin/manage-avis/update', 'UtilisateurAvisController', 'adminUpdateAvis');

$router->addRoute('GET', BASE_URL . 'admin/manage-habitat', 'UtilisateurHabitatController', 'adminHabitatPage');
$router->addRoute('POST', BASE_URL . 'admin/manage-habitat/add', 'UtilisateurHabitatController', 'adminAddHabitat');
$router->addRoute('GET', BASE_URL . 'admin/manage-habitat/action/success', 'UtilisateurHabitatController', 'adminSuccessActionHabitat');
$router->addRoute('POST', BASE_URL . 'admin/manage-habitat/delete', 'UtilisateurHabitatController', 'adminDeleteHabitat');
$router->addRoute('POST', BASE_URL . 'admin/manage-habitat/deleteimg', 'UtilisateurHabitatController', 'adminDeleteHabitatImg');
$router->addRoute('GET', BASE_URL . 'admin/manage-habitat/update', 'UtilisateurHabitatController', 'adminUpdateHabitatPage');
$router->addRoute('POST', BASE_URL . 'admin/manage-habitat/update', 'UtilisateurHabitatController', 'adminUpdateHabitat');

$router->addRoute('GET', BASE_URL . 'admin/manage-habitat-vete', 'UtilisateurHabitatVeteController', 'adminHabitatPage');
$router->addRoute('GET', BASE_URL . 'admin/manage-habitat-vete/action/success', 'UtilisateurHabitatVeteController', 'adminSuccessActionHabitat');
$router->addRoute('GET', BASE_URL . 'admin/manage-habitat-vete/update', 'UtilisateurHabitatVeteController', 'adminUpdateHabitatPage');
$router->addRoute('POST', BASE_URL . 'admin/manage-habitat-vete/update', 'UtilisateurHabitatVeteController', 'adminUpdateHabitat');

$router->addRoute('GET', BASE_URL . 'admin/manage-animaux', 'UtilisateurAnimauxController', 'adminAnimauxPage');
$router->addRoute('POST', BASE_URL . 'admin/manage-animaux/add', 'UtilisateurAnimauxController', 'adminAddAnimaux');
$router->addRoute('GET', BASE_URL . 'admin/manage-animaux/action/success', 'UtilisateurAnimauxController', 'adminSuccessActionAnimaux');
$router->addRoute('POST', BASE_URL . 'admin/manage-animaux/delete', 'UtilisateurAnimauxController', 'adminArchiveAnimaux');
$router->addRoute('POST', BASE_URL . 'admin/manage-animaux/deleteimg', 'UtilisateurAnimauxController', 'adminDeleteAnimauxImg');
$router->addRoute('GET', BASE_URL . 'admin/manage-animaux/update', 'UtilisateurAnimauxController', 'adminUpdateAnimauxPage');
$router->addRoute('POST', BASE_URL . 'admin/manage-animaux/update', 'UtilisateurAnimauxController', 'adminUpdateAnimaux');

$router->addRoute('GET', BASE_URL . 'admin/manage-rapport', 'UtilisateurRapportVeteController', 'adminRapportPage');
$router->addRoute('POST', BASE_URL . 'admin/manage-rapport/add', 'UtilisateurRapportVeteController', 'adminAddRapport');
$router->addRoute('GET', BASE_URL . 'admin/manage-rapport/action/success', 'UtilisateurRapportVeteController', 'adminSuccessActionRapport');
$router->addRoute('POST', BASE_URL . 'admin/manage-rapport/delete', 'UtilisateurRapportVeteController', 'adminDeleteRapport');
$router->addRoute('GET', BASE_URL . 'admin/manage-rapport/update', 'UtilisateurRapportVeteController', 'adminUpdateRapportPage');
$router->addRoute('POST', BASE_URL . 'admin/manage-rapport/update', 'UtilisateurRapportVeteController', 'adminUpdateRapport');

$router->addRoute('GET', BASE_URL . 'admin/manage-nourriture', 'ConsommationNourritureController', 'adminNourriturePage');
$router->addRoute('POST', BASE_URL . 'admin/manage-nourriture/add', 'ConsommationNourritureController', 'adminAddNourriture');
$router->addRoute('GET', BASE_URL . 'admin/manage-nourriture/action/success', 'ConsommationNourritureController', 'adminSuccessActionNourriture');
$router->addRoute('POST', BASE_URL . 'admin/manage-nourriture/delete', 'ConsommationNourritureController', 'adminDeleteNourriture');
$router->addRoute('GET', BASE_URL . 'admin/manage-nourriture/update', 'ConsommationNourritureController', 'adminUpdateNourriturePage');
$router->addRoute('POST', BASE_URL . 'admin/manage-nourriture/update', 'ConsommationNourritureController', 'adminUpdateNourriture');

$router->addRoute('GET', BASE_URL . 'admin/manage-utilisateur', 'UtilisateurCompteController', 'adminUtilisateurPage');
$router->addRoute('POST', BASE_URL . 'admin/manage-utilisateur/add', 'UtilisateurCompteController', 'adminAddUtilisateur');
$router->addRoute('GET', BASE_URL . 'admin/manage-utilisateur/action/success', 'UtilisateurCompteController', 'adminSuccessActionUtilisateur');
$router->addRoute('POST', BASE_URL . 'admin/manage-utilisateur/delete', 'UtilisateurCompteController', 'adminDeleteUtilisateur');
$router->addRoute('GET', BASE_URL . 'admin/manage-utilisateur/update', 'UtilisateurCompteController', 'adminUpdateUtilisateurPage');
$router->addRoute('POST', BASE_URL . 'admin/manage-utilisateur/update', 'UtilisateurCompteController', 'adminUpdateUtilisateur');

$router->addRoute('GET', BASE_URL . 'apigetimganimaux', 'HomeController', 'apiGetImgAnimaux');
$router->addRoute('GET', BASE_URL . 'apigetimghabitats', 'HomeController', 'apiGetImgHabitats');
$router->addRoute('GET', BASE_URL . 'apigetallhabitats', 'HomeController', 'apiGetAllHabitats');
$router->addRoute('GET', BASE_URL . 'apigetservices', 'HomeController', 'apiGetServices');
$router->addRoute('GET', BASE_URL . 'apigetanimauxbyhabitat', 'HomeController', 'apiGetAnimauxByHabitat');
$router->addRoute('GET', BASE_URL . 'apigetanimauxbyidanimal', 'HomeController', 'apiGetAnimauxByIdAnimal');
$router->addRoute('GET', BASE_URL . 'apigetrapportbyidanimal', 'HomeController', 'apiGetRapportByIdAnimal');
$router->addRoute('GET', BASE_URL . 'apigetactiveavis', 'HomeController', 'apiGetActiveAvis');
$router->addRoute('POST', BASE_URL . 'apiaddavis', 'HomeController', 'apiAddAvis');



$method = $_SERVER['REQUEST_METHOD'];
$uri = strtolower($_SERVER['REQUEST_URI']); // gère les minuscules et les majuscules

$handler = $router->gethandler($method, $uri);
if ($handler == null) {

    header('HTTP/1.1 404 not found');
    echo '404';
    exit();
}

$controller = new $handler['controller']();
$action = $handler['action'];
$controller->$action();
