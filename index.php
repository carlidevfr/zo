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
require_once './src/Controller/UtilisateurHomeController.php';
require_once './src/Controller/UtilisateurRaceController.php';
require_once './src/Controller/UtilisateurAvisController.php';



require_once './src/Model/Common/Security.php';
require_once './src/Model/Common/Regenerate.php';


$router = new Router();

$router->addRoute('GET', BASE_URL . '/', 'homecontroller', 'index');

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

$router->addRoute('GET', BASE_URL . 'admin/manage-avis', 'UtilisateurAvisController', 'adminAvisPage');
$router->addRoute('GET', BASE_URL . 'admin/manage-avis/action/success', 'UtilisateurAvisController', 'adminSuccessActionAvis');
$router->addRoute('POST', BASE_URL . 'admin/manage-avis/delete', 'UtilisateurAvisController', 'adminDeleteAvis');
$router->addRoute('GET', BASE_URL . 'admin/manage-avis/update', 'UtilisateurAvisController', 'adminUpdateAvisPage');
$router->addRoute('POST', BASE_URL . 'admin/manage-avis/update', 'UtilisateurAvisController', 'adminUpdateAvis');


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
