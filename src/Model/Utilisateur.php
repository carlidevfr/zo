<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Utilisateur extends Model
{
    public function utilisateurIsValid($email, $password)
    //vérifie si l'utilisateur existe
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
    //vérifie si l'utilisateur existe
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
}