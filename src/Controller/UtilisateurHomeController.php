<?php

require_once './src/Model/Common/Security.php';
require_once './src/Model/Utilisateur.php';


class UtilisateurHomeController
{
    private $Security;
    private $Utilisateur;

    public function __construct()
    {
        $this->Security = new Security();
        $this->Utilisateur = new Utilisateur();
    }

    public function utilisateurHomePage()
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // Affiche la page back office
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurHome.twig');

        echo $template->render([
            'base_url' => BASE_URL,
        ]);
    }


    public function userLogin()
    {
        // on vérifie si l'utilisateur est déjà connecté ou non
        if (
            isset($_SESSION['ipAdress']) and $_SESSION['ipAdress'] === $_SERVER['REMOTE_ADDR'] and
            isset($_SESSION['userAgent']) and $_SESSION['userAgent'] === $_SERVER['HTTP_USER_AGENT'] and
            isset($_SESSION['role']) and $_SESSION['role'] === 'admin' and
            isset($_SESSION['csrf_token'])
        ) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }

        // Affiche le formulaire de connexion et traite ses données
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('formAuth.twig');

        // on vérifie que les valeurs de post sont renseignées
        if (isset($_POST['id']) and !empty($_POST['id']) and isset($_POST['password']) and !empty($_POST['password'])) {

            //on récupère l'information dans une variable
            $id = $this->Security->filter_form($_POST['id']);
            $password = $this->Security->filter_form($_POST['password']);
            $msg = '';

            // on vérifie si les données correspondent à la BDD
            if ($this->Utilisateur->utilisateurIsValid($id, $password)) {

                // on stocke l'uuid et le role dans une variable
                $user = $this->Utilisateur->getUtilisateurId($id, $password);

                if (is_array($user) && !isset($user['error'])) {
                    // on vérifie que l'uuid ne contient pas le mot erreur
                    //Si oui on attribue le role admin (le seul)
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['user'] = $user['id_utilisateur'];

                    // Génère un token aléatoire
                    $_SESSION['csrf_token'] = md5(bin2hex(random_bytes(32)));

                    // Récupère l'ip du visiteur
                    $_SESSION['ipAdress'] = $this->Security->filter_form($_SERVER['REMOTE_ADDR']);

                    // Récupère le navigateur du visiteur
                    $_SESSION['userAgent'] = $this->Security->filter_form($_SERVER['HTTP_USER_AGENT']);

                    //On met le time dans la variable session afin de gérer plus tard le renouvellement d'id
                    $_SESSION['last_id'] = time();

                    //on redirige vers la page admin
                    header('Location: ' . BASE_URL . 'admin');
                    exit;

                } else {
                    $msg = 'Erreur de connexion';
                    session_unset();
                    session_destroy();
                }
            } else {
                $msg = 'Erreur de connexion';
                session_unset();
                session_destroy();
            }

            // En cas de mauvaise connexion on affiche le formulaire et un message
            echo $template->render([
                'base_url' => BASE_URL,
                'message' => $msg
            ]);

        } else {
            // si les post sont vide

            echo $template->render([
                'base_url' => BASE_URL,
            ]);
        }
    }

    public function userLogout()
    // on déconnecte
    {
        return $this->Security->logout();
    }
}
