<?php

require_once './src/Model/Common/Security.php';
require_once './src/Model/Common/SendMail.php';


class ContactController
{
    private $Security;
    private $SendMail;


    public function __construct()
    {
        $this->Security = new Security();
    }

    public function contactPage()
    {
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);

        // On récupère le résultat d'envoi du form s'il existe
        if (isset($_SESSION['resultat']) and !empty($_SESSION['resultat'])) {
            $res = $this->Security->filter_form($_SESSION['resultat']);

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);

        } else {

        $res = '';

        }


        $template = $twig->load('contactPage.twig');
        echo $template->render([
            'base_url' => BASE_URL,
            'res' => $res
        ]);
    }

    public function contactForm()
    {
        //récupération et envoi du contenu du formulaire par email

        (isset($_POST['Nom'])) ? $nom = $this->Security->filter_form($_POST['Nom']) : $nom = '';
        (isset($_POST['email'])) ? $email = $this->Security->filter_form($_POST['email']) : $email = '';
        (isset($_POST['message'])) ? $message = $this->Security->filter_form($_POST['message']) : $message = '';


        $send = new SendMail($this->Security->filter_form($_ENV['FROM']), $this->Security->filter_form($_ENV['FROM']), $this->Security->filter_form($nom), $this->Security->filter_form($email), $this->Security->filter_form($message));
        if ($send->mailSend()) {
            $res = "Votre demande est bien transmise";
        } else {
            $res = "Echec de l'envoi du message";
        }
        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        
        header('Location: ' . BASE_URL . 'contact');
        exit;
    }

}
