<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Utilisateur extends Model
{
    public function utilisateurIsValid($email, $password)
    //vérifie si l'utilisateur existe et si le mdp est ok
    {
        try {
            // récupère le mot de passe hashé

            $bdd = $this->connexionPDO();
            $req = '
            SELECT pass
            FROM utilisateurs
            WHERE email  = :email
            LIMIT 1';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($email)) {
                    $stmt->bindValue(':email', $email, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        $bddPassword = $stmt->fetchColumn();
                        $stmt->closeCursor();
                        if (isset($bddPassword) and !empty($bddPassword) and password_verify($password, $bddPassword)) {
                            //si on a un résultat et si le hash correspond au mot de passe renseigné
                            return true;
                        } else {
                            return false;
                        }
                    }
                } else {
                    return 'une erreur est survenue';
                }
            }
        } catch (Exception $e) {
            $this->logError($e); // Enregistrer l'erreur dans le fichier de log
        }
    }

    public function getUtilisateurId($email, $password)
    //vérifie si l'utilisateur existe et vérifie si le mdp est ok
    {
        try {
            // Vérifie si l'id et le mdp correspondent à un utilisateur
            if ($this->utilisateurIsValid($email, $password) === true) {
                //Si oui, on récupère l'UUID
                $bdd = $this->connexionPDO();
                $req = '
                SELECT id_utilisateur,
                roles.label AS role
                FROM utilisateurs
                INNER JOIN roles ON utilisateurs.role_utilisateur = roles.id_role
                WHERE email  = :email
                LIMIT 1';

                if (is_object($bdd)) {
                    // on teste si la connexion pdo a réussi
                    $stmt = $bdd->prepare($req);

                    if (!empty($email)) {
                        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

                        if ($stmt->execute()) {
                            $idAdmin = $stmt->fetch(PDO::FETCH_ASSOC);
                            $stmt->closeCursor();
                            return $idAdmin;
                        }
                    } else {
                        return ['error' => 'une erreur est survenue'];
                    }
                }
            } else {
                return ['error' => 'une erreur est survenue'];
            }

        } catch (Exception $e) {
            $this->logError($e); // Enregistrer l'erreur dans le fichier de log
        }
    }

    public function getPaginationAllUtilisateurNames($page, $itemsPerPage)
    //Récupère les utilisateurs triés par page
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
            SELECT 
            utilisateurs.id_utilisateur AS id,
            utilisateurs.firstname,
            utilisateurs.lastname,
            utilisateurs.email AS valeur,
            roles.label AS role
            FROM utilisateurs
            JOIN 
            roles ON utilisateurs.role_utilisateur = roles.id_role
            ORDER BY utilisateurs.id_utilisateur
            LIMIT :offset, :itemsPerPage';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $users;
                    }
                }

            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getAllUtilisateurNames()
    //Récupère tous les utilisateurs
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            SELECT 
            utilisateurs.id_utilisateur AS id,
            utilisateurs.firstname,
            utilisateurs.lastname,
            utilisateurs.email AS valeur,
            roles.label AS role
            FROM utilisateurs
            JOIN 
            roles ON utilisateurs.role_utilisateur = roles.id_role
            ORDER BY utilisateurs.id_utilisateur';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $users;
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getAllRoleNames()
    //Récupère tous les roles
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            SELECT 
            id_role AS id,
            label AS valeur
            FROM roles
            ORDER BY id_role';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $users;
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getSearchUtilisateurNames($Name, $page, $itemsPerPage)
    //Récupère les utilisateurs recherchés triés par page
    // si vide retourne tout
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
            SELECT 
            utilisateurs.id_utilisateur AS id,
            utilisateurs.firstname,
            utilisateurs.lastname,
            utilisateurs.email AS valeur,
            roles.label AS role
            FROM utilisateurs
            JOIN 
            roles ON utilisateurs.role_utilisateur = roles.id_role
            WHERE utilisateurs.firstname LIKE :nom OR
            utilisateurs.lastname LIKE :nom OR
            utilisateurs.email LIKE :nom OR
            roles.label LIKE :nom
            ORDER BY utilisateurs.id_utilisateur
            LIMIT :offset, :itemsPerPage';
            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($Name) and !empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':nom', '%' . $Name . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $res;
                    }
                } else {
                    return $this->getAllUtilisateurNames();
                }
            } else {
                return 'une erreur est survenue';
            }

        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getByUtilisateurId($id)
    //retourne un utilisateur selon l'id
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            SELECT 
            utilisateurs.id_utilisateur AS id,
            utilisateurs.firstname,
            utilisateurs.lastname,
            utilisateurs.email AS valeur,
            roles.label AS role
            FROM utilisateurs
            JOIN 
            roles ON utilisateurs.role_utilisateur = roles.id_role
            WHERE utilisateurs.id_utilisateur  = :id';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($id)) {
                    $stmt->bindValue(':id', $id, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $user;
                    }
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function addUtilisateur($utilisateurName, $utilisateurFirstname, $utilisateurEmail, $utilisateurPass, $utilisateurRole)
    // ajoute un utilisateur et vérifie si le mdp est fort
    {
        try {
            // Connexion à la base de données
            $bdd = $this->connexionPDO();

            // Requête d'insertion
            $req = '
        INSERT INTO utilisateurs (id_utilisateur, firstname, lastname, email, pass, role_utilisateur)
        VALUES (UUID(), :utilisateurFirstname, :utilisateurName, :utilisateurEmail, :utilisateurPass, :utilisateurRole)';

            if (is_object($bdd)) {
                // Vérifier si tous les champs sont remplis
                if (!empty($utilisateurName) && !empty($utilisateurFirstname) && !empty($utilisateurEmail) && !empty($utilisateurPass) && !empty($utilisateurRole)) {

                    if (!$this->isPasswordValid($utilisateurPass)) {
                        return 'Le mot de passe ne respecte pas les critères de sécurité.';
                    }
                    // Hacher le mot de passe
                    $hashedPass = password_hash($utilisateurPass, PASSWORD_BCRYPT);

                    // Préparer la requête
                    $stmt = $bdd->prepare($req);

                    // Lier les valeurs
                    $stmt->bindValue(':utilisateurName', $utilisateurName, PDO::PARAM_STR);
                    $stmt->bindValue(':utilisateurFirstname', $utilisateurFirstname, PDO::PARAM_STR);
                    $stmt->bindValue(':utilisateurEmail', $utilisateurEmail, PDO::PARAM_STR);
                    $stmt->bindValue(':utilisateurPass', $hashedPass, PDO::PARAM_STR);
                    $stmt->bindValue(':utilisateurRole', $utilisateurRole, PDO::PARAM_INT);

                    // Exécuter la requête
                    if ($stmt->execute()) {
                        return 'cet utilisateur a bien été ajouté : ' . $utilisateurEmail;
                    } else {
                        return 'Une erreur est survenue';
                    }
                } else {
                    return 'Tous les champs doivent être remplis.';
                }
            } else {
                return 'Une erreur est survenue lors de la connexion à la base de données.';
            }
        } catch (Exception $e) {
            // Logger l'erreur
            $this->logError($e);
            return 'Une erreur est survenue : ' . $e->getMessage();
        }
    }

    private function isPasswordValid($password)
    {
        $regex = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{12,}$/';
        return preg_match($regex, $password);
    }
    public function deleteUtilisateur($Id)
    {
        try {
            // Supprime un utilisateur selon l'id

            $bdd = $this->connexionPDO();
            $req = '
            DELETE FROM utilisateurs
            WHERE id_utilisateur  = :Id';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($Id)) {
                    $stmt->bindValue(':Id', $Id, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'Cet utilisateur a bien été supprimé ';
                    }
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
            return 'Une erreur est survenue';
        }
    }

    public function updateUtilisateur($Id, $utilisateurName, $utilisateurFirstname, $utilisateurEmail, $utilisateurPass, $utilisateurRole)
    // Modifie le utilisateur selon l'id
    {
        try {
            $bdd = $this->connexionPDO();

            if (empty($Id)) {
                return 'une erreur est survenue';
            } else {
                // Ajout dans la table rapport
                $req = 'UPDATE utilisateurs SET ';

                $params = [];

                if (!empty($utilisateurFirstname)) {
                    $req .= 'firstname = :utilisateurFirstname, ';
                    $params[':utilisateurFirstname'] = $utilisateurFirstname;
                }

                if (!empty($utilisateurName)) {
                    $req .= 'lastname = :utilisateurName, ';
                    $params[':utilisateurName'] = $utilisateurName;
                }

                if (!empty($utilisateurEmail)) {
                    $req .= 'email = :utilisateurEmail, ';
                    $params[':utilisateurEmail'] = $utilisateurEmail;
                }

                if (!empty($utilisateurPass)) {

                    if (!$this->isPasswordValid($utilisateurPass)) {
                        return 'Le mot de passe ne respecte pas les critères de sécurité.';
                    }

                    // Hacher le mot de passe
                    $hashedPass = password_hash($utilisateurPass, PASSWORD_BCRYPT);
                    $req .= 'pass = :hashedPass, ';
                    $params[':hashedPass'] = $hashedPass;
                }

                if (!empty($utilisateurRole)) {
                    $req .= 'role_utilisateur = :utilisateurRole, ';
                    $params[':utilisateurRole'] = $utilisateurRole;
                }

                $req = rtrim($req, ', '); // Supprimer la virgule en trop à la fin de la requête

                $req .= ' WHERE id_utilisateur = :id_utilisateur';
            }

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($Id)) {

                    foreach ($params as $paramName => $paramValue) {
                        $stmt->bindValue($paramName, $paramValue, PDO::PARAM_STR);
                    }

                    $stmt->bindValue(':id_utilisateur', $Id, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        return "Cet utilisateur a été mis à jour avec succès.";
                    }
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
            return 'Une erreur est survenue';
        }
    }
}