<?php

class Security
{
    // Cette fonction filtre les données d'un formulaire en enlevant les espaces inutiles en début et fin de chaîne, en supprimant les antislashes ajoutés pour échapper les caractères spéciaux et en convertissant les caractères spéciaux en entités HTML. Elle renvoie les données filtrées.
    public static function filter_form($data)
    {
        $data = trim((string) $data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }


    public static function filter_form_array($data)
    {
        // Vérifie si $data est un tableau
        if (!is_array($data)) {
            return self::filter_form($data);
        }

        // Initialiser un tableau pour stocker les données nettoyées
        $cleaned_data = array();

        // Itérer sur chaque élément du tableau d'entrée
        foreach ($data as $key => $value) {
            // Appliquer la fonction filter_form() à chaque valeur
            $cleaned_data[$key] = self::filter_form($value);
        }

        // Retourne le tableau nettoyé
        return $cleaned_data;
    }

    public static function verifyAccess()
    // on vérifie si l'utilisateur a le droit d'être là, sinon on détruit la session et on le redirige vers l'accueil
    {
        //On vérifie si l'adresse ip est la même que lors de la connexion
        //Si le navigateur est le même
        //Si le rôle existe
        // si un token existe
        if (
            isset($_SESSION['ipAdress']) and $_SESSION['ipAdress'] === $_SERVER['REMOTE_ADDR'] and
            isset($_SESSION['userAgent']) and $_SESSION['userAgent'] === $_SERVER['HTTP_USER_AGENT'] and
            isset($_SESSION['role']) and isset($_SESSION['csrf_token']) and isset($_SESSION['user']) and
            in_array($_SESSION['role'], ['veterinaire', 'admin', 'employe'])
        ) {
            // On vérifie si on regénère l'id de session
            if (isset($_SESSION['last_id']) and time() - $_SESSION['last_id'] > 10) {
                session_regenerate_id(true);
                $_SESSION['last_id'] = time();
            }

            // on autorise la connexion
            return true;
        } else {
            session_unset();
            session_destroy();
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
    }

    public static function regenerateToken()
    // on vérifie si l'utilisateur a le droit d'être là, sinon on détruit la session et on le redirige vers l'accueil
    //et on regénère le token
    {
        //On vérifie si l'adresse ip est la même que lors de la connexion
        //Si le navigateur est le même
        //Si le rôle est admin
        // si un token existe
        if (
            isset($_SESSION['ipAdress']) and $_SESSION['ipAdress'] === $_SERVER['REMOTE_ADDR'] and
            isset($_SESSION['userAgent']) and $_SESSION['userAgent'] === $_SERVER['HTTP_USER_AGENT'] and
            isset($_SESSION['role']) and isset($_SESSION['csrf_token']) and isset($_SESSION['user'])
        ) {
            // On regénère le token
            $_SESSION['csrf_token'] = md5(bin2hex(random_bytes(32)));

            // On vérifie si on regénère l'id de session
            if (isset($_SESSION['last_id']) and time() - $_SESSION['last_id'] > 10) {
                session_regenerate_id(true);
                $_SESSION['last_id'] = time();
            }

            // on autorise la connexion
            return true;
        } else {
            session_unset();
            session_destroy();
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
    }

    public static function getToken()
    // On récupère le token
    // Si absent on déconnecte
    {
        if (isset($_SESSION['csrf_token']) and !empty($_SESSION['csrf_token'])) {
            return self::filter_form($_SESSION['csrf_token']);

        } else {
            session_unset();
            session_destroy();
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
    }

    public static function getRole()
    // On récupère le role
    // Si absent on déconnecte
    {
        if (isset($_SESSION['role']) and !empty($_SESSION['role'])) {
            return self::filter_form($_SESSION['role']);

        } else {
            session_unset();
            session_destroy();
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
    }

    public static function verifyToken($token, $form)
    // On vérifie le token du form et celui en session
    // Si faux on ne traite pas le form
    {
        if (
            isset($token) and !empty($token) and
            isset($form) and !empty($form) and
            $form === $token
        ) {
            return true;

        } else {
            self::logOut();
            return false;
        }
    }

    public static function logout()
    //  on déconnecte
    {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . 'login');
        exit;
    }

    public static function verifyImg($file)
    //  on vérifie les images téléchargées
    {

        // Vérifier s'il y a des fichiers téléchargés
        if (!isset($file) || empty($file)) {
            return null;
        }

        $res = [];

        // Parcourir chaque fichier téléchargé dans le tableau $_FILES
        foreach ($file['name'] as $index => $file_name) {
            // On vérifie si vide
            if (empty($file['tmp_name'][$index]) or empty($file['name'][$index])) {
                return null;
            } else {
                // récupération des informations de notre fichier 

                $file_name = strip_tags($file['name'][$index]);
                $file_error = $file['error'][$index];
                $file_size = $file['size'][$index];
                $file_type = $file['type'][$index];
                $file_tmp = $file['tmp_name'][$index];
                $file_ext = explode('.', $file_name);
                $file_end = end($file_ext);  // jpg $
                $file_end = strtolower($file_end);
                $extensions = ['jpg', 'jpeg', 'png'];
                $allowedMimeTypes = ['image/jpeg', 'image/png'];
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($file['tmp_name'][$index]);

                if (isset($file_error) && $file_error !== UPLOAD_ERR_OK) {
                    // Si le fichier est en erreur
                    return null;
                } elseif ($file_size > 0.1 * 1024 * 1024) {
                    // Si la taille du fichier dépasse la limite autorisée, on affiche un message d'erreur
                    return null;

                } elseif (in_array($file_end, $extensions) === false or !in_array($mimeType, $allowedMimeTypes)) {
                    // Si l'extension du fichier n'est pas autorisée, on affiche un message d'erreur
                    return null;

                } else {

                    // Générer une chaîne aléatoire de 3 chiffres
                    //$random_suffix = mt_rand(100, 999);

                    // On nettoie le nom de fichier en supprimant les caractères spéciaux
                    //$file_end = preg_replace('/[^A-Za-z0-9.\-]/', '', $file_end);

                    // Nouveau nom de fichier avec la chaîne aléatoire ajoutée avant l'extension
                    //$new_file_name = $file_name . '-' . $random_suffix . '.' . $file_end;

                    // On déplace le fichier uploadé vers le répertoire "uploads" avec son nom d'origine
                    //move_uploaded_file($file_tmp, "./public/assets/" . $new_file_name);
                    // On affiche un message de succès pour indiquer que le téléchargement a réussi
                    //return " Le fichier " . $file_name . " a été téléchargé avec succès";
                    $res[$index]['data'] = file_get_contents($file_tmp);
                    $res[$index]['type'] = $file_type;
                }
            }
        }
        return $res;
    }

}
