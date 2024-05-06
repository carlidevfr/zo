<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class RapportVeterinaire extends Model
{
    public function getSearchRapportNames($rapportName, $page, $itemsPerPage)
    //Récupère les rapports recherchés triées par page
    // si vide retourne tout
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = "
            SELECT rapport_veterinaire.id_rapport AS id,
            DATE_FORMAT(rapport_veterinaire.date_rapport, '%d/%m/%Y') AS valeur,
            rapport_veterinaire.animal,
            rapport_veterinaire.nourriture_propose,
            rapport_veterinaire.quantite_nourriture,
            rapport_veterinaire.detail,
            animaux.nom_animal,
            animaux.etat
            FROM rapport_veterinaire
            INNER JOIN animaux ON rapport_veterinaire.animal = animaux.id_animal
            WHERE rapport_veterinaire.nourriture_propose LIKE :search OR
            rapport_veterinaire.detail LIKE :search OR
            DATE_FORMAT(rapport_veterinaire.date_rapport, '%d/%m/%Y') LIKE :search OR
            animaux.nom_animal LIKE :search
            ORDER BY rapport_veterinaire.id_rapport DESC
            LIMIT :offset, :itemsPerPage";
            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($rapportName) and !empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':search', '%' . $rapportName . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $races = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $races;
                    }
                } else {
                    return $this->getAllRapportNames();
                }
            } else {
                return 'une erreur est survenue';
            }

        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getAllRapportNames()
    //Récupère tous les rapports
    {
        try {
            $bdd = $this->connexionPDO();
            $req = "
            SELECT rapport_veterinaire.id_rapport AS id,
            DATE_FORMAT(rapport_veterinaire.date_rapport, '%d/%m/%Y') AS valeur,
            rapport_veterinaire.animal,
            rapport_veterinaire.nourriture_propose,
            rapport_veterinaire.quantite_nourriture,
            rapport_veterinaire.detail,
            animaux.nom_animal,
            animaux.etat
            FROM rapport_veterinaire
            INNER JOIN animaux ON rapport_veterinaire.animal = animaux.id_animal
            ORDER BY rapport_veterinaire.id_rapport DESC";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $races = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $races;
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getPaginationAllRapportNames($page, $itemsPerPage)
    //Récupère les rapports triés par page
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = "
            SELECT rapport_veterinaire.id_rapport AS id,
            DATE_FORMAT(rapport_veterinaire.date_rapport, '%d/%m/%Y') AS valeur,
            rapport_veterinaire.animal,
            rapport_veterinaire.nourriture_propose,
            rapport_veterinaire.quantite_nourriture,
            rapport_veterinaire.detail,
            animaux.nom_animal,
            animaux.etat
            FROM rapport_veterinaire
            INNER JOIN animaux ON rapport_veterinaire.animal = animaux.id_animal
            ORDER BY rapport_veterinaire.id_rapport DESC
            LIMIT :offset, :itemsPerPage";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $races = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $races;
                    }
                }

            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getByRapportId($rapportId)
    //retourne le rapport selon l'id
    {
        try {
            $bdd = $this->connexionPDO();
            $req = "
            SELECT rapport_veterinaire.id_rapport AS id,
            DATE_FORMAT(rapport_veterinaire.date_rapport, '%d/%m/%Y') AS valeur,
            rapport_veterinaire.animal,
            rapport_veterinaire.nourriture_propose,
            rapport_veterinaire.quantite_nourriture,
            rapport_veterinaire.detail,
            animaux.nom_animal,
            animaux.etat
            FROM rapport_veterinaire
            INNER JOIN animaux ON rapport_veterinaire.animal = animaux.id_animal
            WHERE rapport_veterinaire.id_rapport = :rapportId";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($rapportId)) {
                    $stmt->bindValue(':rapportId', $rapportId, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $race = $stmt->fetch(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $race;
                    }
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function addRapport($addDate, $addAnimal, $addSante, $addNou, $addQte, $addRapport)
    // Ajoute un rapport
    {
        try {
            if (empty($addDate) || empty($addAnimal) || empty($addNou) || empty($addQte) || empty($addRapport)) {
                return "Veuillez fournir toutes les informations nécessaires.";
            }

            $bdd = $this->connexionPDO();

            // Commencer la transaction
            $bdd->beginTransaction();

            $sql_insert_rapport = '
            INSERT INTO rapport_veterinaire (date_rapport, detail, nourriture_propose, quantite_nourriture, animal)
            VALUES (:date_rapport, :detail, :nourriture_propose, :quantite_nourriture, :id_animal)';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt_insert_rapport = $bdd->prepare($sql_insert_rapport);

                $stmt_insert_rapport->bindValue(':date_rapport', $addDate, PDO::PARAM_STR);
                $stmt_insert_rapport->bindValue(':detail', $addRapport, PDO::PARAM_STR);
                $stmt_insert_rapport->bindValue(':nourriture_propose', $addNou, PDO::PARAM_STR);
                $stmt_insert_rapport->bindValue(':quantite_nourriture', $addQte, PDO::PARAM_STR);
                $stmt_insert_rapport->bindValue(':id_animal', $addAnimal, PDO::PARAM_INT);

                if ($stmt_insert_rapport->execute()) {
                    if (!empty($addSante)) {

                        // Mise à jour de l'état de l'animal
                        $sql_update_animal = "UPDATE animaux SET etat = :etat_animal WHERE id_animal = :id_animal";
                        $stmt_update_animal = $bdd->prepare($sql_update_animal);
                        $stmt_update_animal->bindValue(":etat_animal", $addSante);
                        $stmt_update_animal->bindValue(":id_animal", $addAnimal);
                        $stmt_update_animal->execute();
                    }
                    // Validation de la transaction
                    $bdd->commit();
                    return "Le rapport vétérinaire a été ajouté avec succès et l'état de l'animal a été modifié.";
                }

            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $bdd->rollback();
            $this->logError($e);
            return 'Une erreur est survenue';
        }
    }

    public function deleteRapport($rapportId)
    {
        try {
            // Supprime le rapport selon l'id

            $bdd = $this->connexionPDO();
            $req = '
            DELETE FROM rapport_veterinaire
            WHERE id_rapport  = :rapportId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($rapportId)) {
                    $stmt->bindValue(':rapportId', $rapportId, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        return 'Le rapport a bien été supprimé ';
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

    public function updateRapport($rapportAction, $addDate, $addAnimal, $addSante, $addNou, $addQte, $addRapport)
    // modifie un rapport
    {
        try {
            $bdd = $this->connexionPDO();

            // Commencer la transaction
            $bdd->beginTransaction();
            if (empty($rapportAction)) {
                return 'une erreur est survenue';
            } else {
                // Ajout dans la table rapport
                $req = 'UPDATE rapport_veterinaire SET ';

                $params = [];

                if (!empty($addDate)) {
                    $req .= 'date_rapport = :date_rapport, ';
                    $params[':date_rapport'] = $addDate;
                }

                if (!empty($addAnimal)) {
                    $req .= 'animal = :animal, ';
                    $params[':animal'] = $addAnimal;
                }

                if (!empty($addNou)) {
                    $req .= 'nourriture_propose = :nourriture_propose, ';
                    $params[':nourriture_propose'] = $addNou;
                }

                if (!empty($addQte)) {
                    $req .= 'quantite_nourriture   = :quantite_nourriture, ';
                    $params[':quantite_nourriture'] = $addQte;
                }

                if (!empty($addRapport)) {
                    $req .= 'detail = :detail, ';
                    $params[':detail'] = $addRapport;
                }

                $req = rtrim($req, ', '); // Supprimer la virgule en trop à la fin de la requête

                $req .= ' WHERE id_rapport = :rapportAction';
            }

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($rapportAction)) {

                    foreach ($params as $paramName => $paramValue) {
                        $stmt->bindValue($paramName, $paramValue, PDO::PARAM_STR);
                    }

                    $stmt->bindValue(':rapportAction', $rapportAction, PDO::PARAM_INT);

                    if ($stmt->execute()) {

                        if (!empty($addSante)) {
                            // Mise à jour de l'état de l'animal
                            $sql_update_animal = "UPDATE animaux SET etat = :etat_animal WHERE id_animal = :id_animal";
                            $stmt_update_animal = $bdd->prepare($sql_update_animal);
                            $stmt_update_animal->bindValue(":etat_animal", $addSante);
                            $stmt_update_animal->bindValue(":id_animal", $addAnimal);
                            $stmt_update_animal->execute();
                        }
                        // Validation de la transaction
                        $bdd->commit();
                        return "Le rapport vétérinaire a été mis à jour avec succès.";
                    }
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $bdd->rollback();
            $this->logError($e);
            return 'Une erreur est survenue';
        }
    }
}