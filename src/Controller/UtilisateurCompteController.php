<?php

require_once './src/Model/Utilisateur.php';
require_once './src/Model/Common/Security.php';

class UtilisateurCompteController
{
    private $Utilisateur;
    private $Security;

    public function __construct()
    {
        $this->Utilisateur = new Utilisateur();
        $this->Security = new Security();
    }

    public function adminUtilisateurPage()
    // Accueil admin de la section utilisateur
    {

        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin') {
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
            $utilisateurs = $this->Utilisateur->getSearchUtilisateurNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();

        } else {
            $utilisateurs = $this->Utilisateur->getPaginationAllUtilisateurNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        if (!empty($this->Utilisateur->getAllUtilisateurNames())) {
            $pageMax = ceil(count($this->Utilisateur->getAllUtilisateurNames()) / $itemsPerPage);
        } else {
            $pageMax = 1;
        }

        // On récupère les rôles

        $roles = $this->Utilisateur->getAllRoleNames();

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurComptes.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'utilisateurs',
            'elements' => $utilisateurs,
            'roles' => $roles,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'deleteUrl' => 'admin/manage-utilisateur/delete',
            'addUrl' => 'admin/manage-utilisateur/add',
            'updateUrl' => 'admin/manage-utilisateur/update',
            'previousUrl' => 'admin/manage-utilisateur',
            'token' => $token
        ]);
    }

    public function adminSuccessActionUtilisateur()
    // Résultat succès ou echec après action sur utilisateur
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin') {
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
            header('Location: ' . BASE_URL . 'admin/manage-utilisateur');
            exit;

        }

        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurComptes.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'utilisateurs',
            'addResult' => $res,
            'deleteUrl' => 'admin/manage-utilisateur/delete',
            'addUrl' => 'admin/manage-utilisateur/add',
            'updateUrl' => 'admin/manage-utilisateur/update',
            'previousUrl' => 'admin/manage-utilisateur'
        ]);

    }

    public function adminAddUtilisateur()
    // Ajout des utilisateurs
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère le nouveau utilisateur ajouté et le token
        if (isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {
            // le nom 
            (isset($_POST['addElementName']) and !empty($_POST['addElementName'])) ? $utilisateurName = $this->Security->filter_form($_POST['addElementName']) : $utilisateurName = '';

            //le prénom
            (isset($_POST['addElementFirstName']) and !empty($_POST['addElementFirstName'])) ? $utilisateurFirstname = $this->Security->filter_form($_POST['addElementFirstName']) : $utilisateurFirstname = '';

            // le mail 
            (isset($_POST['email']) and !empty($_POST['email'])) ? $utilisateurEmail = $this->Security->filter_form($_POST['email']) : $utilisateurEmail = '';

            //le pass
            (isset($_POST['password']) and !empty($_POST['password'])) ? $utilisateurPass = $this->Security->filter_form($_POST['password']) : $utilisateurPass = '';

            // le role
            (isset($_POST['addElementRole']) and !empty($_POST['addElementRole'])) ? $utilisateurRole = $this->Security->filter_form($_POST['addElementRole']) : $utilisateurRole = '';


            // on fait l'ajout en BDD et on récupère le résultat
            $res = $this->Utilisateur->addUtilisateur($utilisateurName, $utilisateurFirstname, $utilisateurEmail, $utilisateurPass, $utilisateurRole);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-utilisateur/action/success');
        exit;
    }

    public function adminDeleteUtilisateur()
    // Suppression de utilisateur
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id pays à supprimer
        (isset($_POST['deleteElementId']) and !empty($_POST['deleteElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $utilisateurAction = $this->Security->filter_form($_POST['deleteElementId']) : $utilisateurAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Utilisateur->deleteUtilisateur($utilisateurAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $utilisateurAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-utilisateur/action/success');
        exit;


    }

    public function adminUpdateUtilisateurPage()
    // Page permettant la saisie pour la modification du utilisateur
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère l'id à modifier
        (isset($_GET['UpdateElementId']) and !empty($_GET['UpdateElementId']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $utilisateurAction = $this->Security->filter_form($_GET['UpdateElementId']) : $utilisateurAction = '';

        // Récupère l'élément à modifier
        $utilisateur = $this->Utilisateur->getByUtilisateurId($utilisateurAction);
        $modifySection = true;

        // On récupère les rôles

        $roles = $this->Utilisateur->getAllRoleNames();

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurComptes.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'utilisateurs',
            'elements' => $utilisateur,
            'modifySection' => $modifySection,
            'roles' => $roles,
            'deleteUrl' => 'admin/manage-utilisateur/delete',
            'addUrl' => 'admin/manage-utilisateur/add',
            'updateUrl' => 'admin/manage-utilisateur/update',
            'previousUrl' => 'admin/manage-utilisateur',
            'token' => $token
        ]);

    }

    public function adminUpdateUtilisateur()
    // Modification de utilisateur
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        if (isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {

            // id
            (isset($_POST['updateElementId']) and !empty($_POST['updateElementId'])) ? $id = $this->Security->filter_form($_POST['updateElementId']) : $id = '';

            // le nom 
            (isset($_POST['addElementName']) and !empty($_POST['addElementName'])) ? $utilisateurName = $this->Security->filter_form($_POST['addElementName']) : $utilisateurName = '';

            //le prénom
            (isset($_POST['addElementFirstName']) and !empty($_POST['addElementFirstName'])) ? $utilisateurFirstname = $this->Security->filter_form($_POST['addElementFirstName']) : $utilisateurFirstname = '';

            // le mail 
            (isset($_POST['email']) and !empty($_POST['email'])) ? $utilisateurEmail = $this->Security->filter_form($_POST['email']) : $utilisateurEmail = '';

            //le pass
            (isset($_POST['password']) and !empty($_POST['password'])) ? $utilisateurPass = $this->Security->filter_form($_POST['password']) : $utilisateurPass = '';

            // le role
            (isset($_POST['addElementRole']) and !empty($_POST['addElementRole'])) ? $utilisateurRole = $this->Security->filter_form($_POST['addElementRole']) : $utilisateurRole = '';


            // on fait la modif en BDD et on récupère le résultat
            $res = $this->Utilisateur->updateUtilisateur($id, $utilisateurName, $utilisateurFirstname, $utilisateurEmail, $utilisateurPass, $utilisateurRole);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-utilisateur/action/success');
        exit;


    }
}