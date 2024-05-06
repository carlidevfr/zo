<?php

require_once './src/Model/Service.php';
require_once './src/Model/Common/Security.php';

class UtilisateurServiceController
{
    private $Service;
    private $Security;

    public function __construct()
    {
        $this->Service = new Service();
        $this->Security = new Security();
    }

    public function adminServicePage()
    // Accueil admin de la section service
    {

        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'employe' and $userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère  la pagination
        (isset($_GET['page']) and !empty($_GET['page'])) ? $page = max(1, $this->Security->filter_form($_GET['page'])) : $page = 1;

        // Nombre d'éléments par page
        $itemsPerPage = 10;

        //Récupère le résultat de la recherche et la valeur de search pour permettre un get sur le search avec la pagination
        if (isset($_GET['search']) and !empty($_GET['search']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) {
            $services = $this->Service->getSearchServiceNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();
            
        } else {
            $services = $this->Service->getPaginationAllServiceNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        if (!empty($this->Service->getAllServiceNames())) {
            $pageMax = ceil(count($this->Service->getAllServiceNames()) / $itemsPerPage);
        }else{
            $pageMax = 1;
        }
        
        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurService.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'services',
            'elements' => $services,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'deleteUrl' => 'admin/manage-service/delete',
            'addUrl' => 'admin/manage-service/add',
            'updateUrl' => 'admin/manage-service/update',
            'previousUrl' => 'admin/manage-service',
            'token' => $token
        ]);
    }

    public function adminSuccessActionService()
    // Résultat succès ou echec après action sur service
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'employe' and $userRole !== 'admin') {
            $this->Security->logout();
        }

        $res = null;
        $idElement = null;

        // On récupère le résultat de la requête
        if (isset($_SESSION['resultat']) and !empty($_SESSION['resultat'])) {
            $res = $this->Security->filter_form($_SESSION['resultat']);

            // Si l'id est en variable session on le récupère
            (isset($_SESSION['idElement']) and !empty($_SESSION['idElement'])) ? $idElement = $this->Security->filter_form($_SESSION['idElement']) : $idElement = '';

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);
            unset($_SESSION['idElement']);

        } else {

            //Si vide on retourne sur la page
            header('Location: ' . BASE_URL . 'admin/manage-service');
            exit;

        }

        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurService.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'services',
            'addResult' => $res,
            'deleteUrl' => 'admin/manage-service/delete',
            'addUrl' => 'admin/manage-service/add',
            'updateUrl' => 'admin/manage-service/update',
            'previousUrl' => 'admin/manage-service'
        ]);

    }

    public function adminAddService()
    // Ajout de services
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'employe' and $userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère le nouveau service ajouté et le token
        if (isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {
            // le nom 
            (isset($_POST['addElementName']) and !empty($_POST['addElementName'])) ? $serviceName = $this->Security->filter_form($_POST['addElementName']) : $serviceName = '';

            //la description
            (isset($_POST['addElementDesc']) and !empty($_POST['addElementDesc'])) ? $serviceDesc = $this->Security->filter_form($_POST['addElementDesc']) : $serviceDesc = '';

           
            // on fait l'ajout en BDD et on récupère le résultat
            $res = $this->Service->addService($serviceName, $serviceDesc);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-service/action/success');
        exit;
    }

    public function adminDeleteService()
    // Suppression de service
    {
       //On vérifie si on a le droit d'être là
       $this->Security->verifyAccess();

       // On récupère le role
       $userRole = $this->Security->getRole();

       if ($userRole !== 'employe' and $userRole !== 'admin') {
        $this->Security->logout();
       }

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id pays à supprimer
        (isset($_POST['deleteElementId']) and !empty($_POST['deleteElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $serviceAction = $this->Security->filter_form($_POST['deleteElementId']) : $serviceAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Service->deleteService($serviceAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $serviceAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-service/action/success');
        exit;


    }

    public function adminUpdateServicePage()
    // Page permettant la saisie pour la modification du service
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'employe' and $userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère l'id à modifier
        (isset($_GET['UpdateElementId']) and !empty($_GET['UpdateElementId']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $serviceAction = $this->Security->filter_form($_GET['UpdateElementId']) : $serviceAction = '';

        // Récupère l'élément à modifier
        $service = $this->Service->getByServiceId($serviceAction);
        $modifySection = true;

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurService.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'services',
            'elements' => $service,
            'modifySection' => $modifySection,
            'deleteUrl' => 'admin/manage-service/delete',
            'addUrl' => 'admin/manage-service/add',
            'updateUrl' => 'admin/manage-service/update',
            'previousUrl' => 'admin/manage-service',
            'token' => $token
        ]);

    }

    public function adminUpdateService()
    // Modification de la service
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'employe' and $userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        if (isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {

            // on récupère l'id de à Modifier
            (isset($_POST['updateElementId']) and !empty($_POST['updateElementId'])) ? $action = $this->Security->filter_form($_POST['updateElementId']) : $action = '';

            // le nom 
            (isset($_POST['addElementName']) and !empty($_POST['addElementName'])) ? $serviceName = $this->Security->filter_form($_POST['addElementName']) : $serviceName = '';

            //la description
            (isset($_POST['addElementDesc']) and !empty($_POST['addElementDesc'])) ? $serviceDesc = $this->Security->filter_form($_POST['addElementDesc']) : $serviceDesc = '';

           
            // on fait la modif en BDD et on récupère le résultat
            $res = $this->Service->updateService($action, $serviceName, $serviceDesc);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-service/action/success');
        exit;


    }
}