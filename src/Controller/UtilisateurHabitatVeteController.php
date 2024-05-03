<?php

require_once './src/Model/Habitat.php';
require_once './src/Model/Common/Security.php';

class UtilisateurHabitatVeteController
{
    private $Habitat;
    private $Security;

    public function __construct()
    {
        $this->Habitat = new Habitat();
        $this->Security = new Security();
    }

    public function adminHabitatPage()
    // Accueil admin de la section habitat
    {

        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'veterinaire') {
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
            $habitat = $this->Habitat->getSearchHabitatNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();

        } else {
            $habitat = $this->Habitat->getPaginationAllHabitatNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        if (!empty($this->Habitat->getAllHabitatNames())) {
            $pageMax = ceil(count($this->Habitat->getAllHabitatNames()) / $itemsPerPage);
        } else {
            $pageMax = 1;
        }

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurHabitatVete.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'habitat',
            'elements' => $habitat,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'updateUrl' => 'admin/manage-habitat-vete/update',
            'previousUrl' => 'admin/manage-habitat-vete',
            'token' => $token
        ]);
    }

    public function adminSuccessActionHabitat()
    // Résultat succès ou echec après action sur habitat
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'veterinaire') {
            $this->Security->logout();
        }

        $res = null;
        $idElement = null;

        // On récupère le résultat de la requête
        if (isset($_SESSION['resultat']) and !empty($_SESSION['resultat'])) {
            $res = $this->Security->filter_form($_SESSION['resultat']);

            // Si l'id est en variable session on le récupère
            (isset($_SESSION['idElement']) and !empty($_SESSION['idElement'])) ? $idElement = $this->Security->filter_form($_SESSION['idElement']) : $idElement = '';

            // On récupère la liste des éléments liés pouvant empêcher la suppression
            (isset($idElement) and !empty($idElement) ? $data = $this->Habitat->getRelatedHabitat($idElement) : $data = '');

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);
            unset($_SESSION['idElement']);

        } else {

            //Si vide on retourne sur la page habitat
            header('Location: ' . BASE_URL . 'admin/manage-habitat-vete');
            exit;

        }

        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurHabitatVete.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'habitat',
            'data' => $data,
            'addResult' => $res,
            'deleteUrl' => 'admin/manage-habitat-vete/delete',
            'addUrl' => 'admin/manage-habitat-vete/add',
            'updateUrl' => 'admin/manage-habitat-vete/update',
            'previousUrl' => 'admin/manage-habitat-vete'
        ]);

    }


    public function adminUpdateHabitatPage()
    // Page permettant la saisie pour la modification de l'habitat'
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'veterinaire') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère l'id habitat à modifier
        (isset($_GET['UpdateElementId']) and !empty($_GET['UpdateElementId']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $habitatAction = $this->Security->filter_form($_GET['UpdateElementId']) : $habitatAction = '';

        // Récupère l'habitat à modifier
        $habitat = $this->Habitat->getByHabitatId($habitatAction);
        $modifySection = true;

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurHabitatVete.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'habitat',
            'elements' => $habitat,
            'modifySection' => $modifySection,
            'addUrl' => 'admin/manage-habitat-vete/add',
            'updateUrl' => 'admin/manage-habitat-vete/update',
            'previousUrl' => 'admin/manage-habitat-vete',
            'token' => $token
        ]);

    }

    public function adminUpdateHabitat()
    // Modification de l'habitat'
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'veterinaire') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();


        if (isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {

            // on récupère l'id de l'habitat à Modifier
            (isset($_POST['updateElementId']) and !empty($_POST['updateElementId'])) ? $habAction = $this->Security->filter_form($_POST['updateElementId']) : $habAction = '';

            // le nom 
            (isset($_POST['updatedName']) and !empty($_POST['updatedName'])) ? $habitatName = $this->Security->filter_form($_POST['updatedName']) : $habitatName = '';


            // on fait la modif en BDD et on récupère le résultat
            $res = $this->Habitat->updateAvisHabitat($habAction, $habitatName);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-habitat-vete/action/success');
        exit;


    }
}