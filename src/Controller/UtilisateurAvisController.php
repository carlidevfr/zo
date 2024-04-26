<?php

require_once './src/Model/Avis.php';
require_once './src/Model/Common/Security.php';

class UtilisateurAvisController
{
    private $Avis;
    private $Security;

    public function __construct()
    {
        $this->Avis = new Avis();
        $this->Security = new Security();
    }

    public function adminAvisPage()
    // Accueil admin de la section avis
    {

        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin' and $userRole !== 'employe') {
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
            $avis = $this->Avis->getSearchAvisNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();
            
        } else {
            $avis = $this->Avis->getPaginationAllAvisNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        if (!empty($this->Avis->getAllAvisNames())) {
            $pageMax = ceil(count($this->Avis->getAllAvisNames()) / $itemsPerPage);
        }else{
            $pageMax = 1;
        }
        
        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurAvis.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'avis',
            'elements' => $avis,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'deleteUrl' => 'admin/manage-avis/delete',
            'addUrl' => 'admin/manage-avis/add',
            'updateUrl' => 'admin/manage-avis/update',
            'previousUrl' => 'admin/manage-avis',
            'token' => $token
        ]);
    }

    public function adminSuccessActionAvis()
    // Résultat succès ou echec après action sur avis
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin' and $userRole !== 'employe') {
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

            //Si vide on retourne sur la page pays
            header('Location: ' . BASE_URL . 'admin/manage-avis');
            exit;

        }

        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurAvis.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'avis',
            'addResult' => $res,
            'deleteUrl' => 'admin/manage-avis/delete',
            'addUrl' => 'admin/manage-avis/add',
            'updateUrl' => 'admin/manage-avis/update',
            'previousUrl' => 'admin/manage-avis'
        ]);

    }
    public function adminDeleteAvis()
    // Suppression de avis
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin' and $userRole !== 'employe') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id AVIS à supprimer
        (isset($_POST['deleteElementId']) and !empty($_POST['deleteElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $avisAction = $this->Security->filter_form($_POST['deleteElementId']) : $avisAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Avis->deleteAvis($avisAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $avisAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-avis/action/success');
        exit;


    }

    public function adminUpdateAvisPage()
    // Page permettant la saisie pour la validation ou non de l'avis
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin' and $userRole !== 'employe') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();
        var_dump($_SESSION['csrf_token']);

        //Récupère l'id de l'avis à modifier
        (isset($_GET['UpdateElementId']) and !empty($_GET['UpdateElementId']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $avisAction = $this->Security->filter_form($_GET['UpdateElementId']) : $avisAction = '';

        // Récupère l'avis à modifier
        $avis = $this->Avis->getByAvisId($avisAction);
        $modifySection = true;

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurAvis.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'avis',
            'element' => $avis,
            'modifySection' => $modifySection,
            'deleteUrl' => 'admin/manage-avis/delete',
            'addUrl' => 'admin/manage-avis/add',
            'updateUrl' => 'admin/manage-avis/update',
            'previousUrl' => 'admin/manage-avis',
            'token' => $token
        ]);

    }

    public function adminUpdateAvis()
    // Modification de l'avis
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin' and $userRole !== 'employe') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id de l avis à Modifier
        (isset($_POST['updateElementId']) and !empty($_POST['updateElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $avisAction = $this->Security->filter_form($_POST['updateElementId']) : $avisAction = '';

        // on récupère le nouveau nom 
        (isset($_POST['updatedName']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $newName = $this->Security->filter_form($_POST['updatedName']) : $newName = '';

        // on fait la modification en BDD et on récupère le résultat
        $res = $this->Avis->updateAvis($avisAction, $newName);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-avis/action/success');
        exit;
    }
}