<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Avis extends Model
{
    public function getSearchAvisNames($search, $page, $itemsPerPage)
    //Récupère les avis recherchées triées par page
    // si vide retourne tout
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = "
            SELECT id_avis AS id,
            pseudo AS valeur, 
            DATE_FORMAT(date_avis, '%d/%m/%Y') AS date_avis,
            contenu_avis,
            actif
            FROM avis
            WHERE pseudo LIKE :search OR
            contenu_avis LIKE :search 
            ORDER BY id_avis DESC
            LIMIT :offset, :itemsPerPage";
            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($search) and !empty ($page) and !empty ($itemsPerPage)) {
                    $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $races = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $races;
                    }
                } else {
                    return $this->getAllAvisNames();
                }
            } else {
                return 'une erreur est survenue';
            }

        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getAllAvisNames()
    //Récupère tous les avis
    {
        try {
            $bdd = $this->connexionPDO();
            $req = "
            SELECT id_avis AS id,
            pseudo AS valeur, 
            DATE_FORMAT(date_avis, '%d/%m/%Y') AS date_avis,
            contenu_avis,
            actif
            FROM avis
            ORDER BY id_avis DESC
            ";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $avis;
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getAllActiveAvisNames()
    //Récupère tous les avis validés
    {
        try {
            $bdd = $this->connexionPDO();
            $req = "
            SELECT id_avis AS id,
            pseudo AS valeur, 
            DATE_FORMAT(date_avis, '%d/%m/%Y') AS date_avis,
            contenu_avis,
            actif
            FROM avis
            WHERE actif = 1
            ORDER BY id_avis DESC
            ";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $avis;
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getPaginationAllAvisNames($page, $itemsPerPage)
    //Récupère les avis triées par page
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = "
            SELECT id_avis AS id,
            pseudo AS valeur, 
            DATE_FORMAT(date_avis, '%d/%m/%Y') AS date_avis,
            contenu_avis,
            actif
            FROM avis
            ORDER BY id_avis DESC
            LIMIT :offset, :itemsPerPage";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty ($page) and !empty ($itemsPerPage)) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $avis;
                    }
                }

            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getByAvisId($avisId)
    //retourne l'avis selon l'id
    {
        try {
            $bdd = $this->connexionPDO();
            $req = "
            SELECT id_avis AS id,
            pseudo AS valeur, 
            DATE_FORMAT(date_avis, '%d/%m/%Y') AS date_avis,
            contenu_avis,
            actif
            FROM avis
            WHERE id_avis  = :avisId";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty ($avisId)) {
                    $stmt->bindValue(':avisId', $avisId, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $avis = $stmt->fetch(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $avis;
                    }
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function deleteAvis($avisId)
    // Supprime l'avis selon l'id
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            DELETE FROM avis
            WHERE id_avis  = :avisId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($avisId)) {
                    $stmt->bindValue(':avisId', $avisId, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return "Cet avis a bien été supprimé ";
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

    public function updateAvis($avisId, $newName)
    // Valide ou non le post de l'avis
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            UPDATE avis
            SET actif = :newName
            WHERE id_avis  = :avisId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($avisId)) {
                    $stmt->bindValue(':avisId', $avisId, PDO::PARAM_INT);
                    $stmt->bindValue(':newName', $newName, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        return 'Le statut de cet avis a bien été modifié ';
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

    public function addAvis($pseudo, $date_avis, $contenu_avis)
    // Ajoute un avis
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            INSERT INTO avis (pseudo, date_avis, contenu_avis, actif)
            VALUES (:pseudo, :date_avis, :contenu_avis, :actif)';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty ($pseudo) && !empty ($date_avis) && !empty ($contenu_avis)) {
                    $stmt->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
                    $stmt->bindValue(':date_avis', $date_avis, PDO::PARAM_STR);
                    $stmt->bindValue(':contenu_avis', $contenu_avis, PDO::PARAM_STR);
                    $stmt->bindValue(':actif', 0, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        return 'Votre avis est en attente de validation';
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