<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class ConsommationNourriture extends Model
{
    public function getSearchNourritureNames($rapportName, $page, $itemsPerPage)
    //Récupère les rapports de nourrrissage recherchés triés par page
    // si vide retourne tout
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = "
            SELECT consommation_nourriture.id_nourrissage AS id,
            DATE_FORMAT(consommation_nourriture.date_nourrissage, '%d/%m/%Y %H:%i') AS valeur,
            animaux.nom_animal,
            consommation_nourriture.nourriture AS nourriture_donnee,
            consommation_nourriture.quantite AS nourriture_donnee_quantite,
            animaux.etat AS etat_sante,
            rapport_veterinaire.nourriture_propose AS info_veterinaire,
            rapport_veterinaire.quantite_nourriture AS quantite_veterinaire,
            DATE_FORMAT(rapport_veterinaire.date_rapport, '%d/%m/%Y') AS date_rapport_veterinaire_format
            FROM consommation_nourriture
            LEFT JOIN animaux ON consommation_nourriture.animal = animaux.id_animal
            LEFT JOIN (
                SELECT *
                FROM rapport_veterinaire rv
                WHERE rv.date_rapport = (
                           SELECT MAX(date_rapport)
                           FROM rapport_veterinaire
                           WHERE animal = rv.animal
                )
            ) AS rapport_veterinaire ON animaux.id_animal = rapport_veterinaire.animal
            WHERE rapport_veterinaire.detail LIKE :search OR
            DATE_FORMAT(consommation_nourriture.date_nourrissage, '%d/%m/%Y %H:%i') LIKE :search OR
            animaux.nom_animal LIKE :search OR
            consommation_nourriture.nourriture LIKE :search OR
            consommation_nourriture.quantite LIKE :search OR
            rapport_veterinaire.nourriture_propose LIKE :search OR
            rapport_veterinaire.quantite_nourriture LIKE :search OR
            animaux.etat LIKE :search
            GROUP BY consommation_nourriture.id_nourrissage
            ORDER BY consommation_nourriture.date_nourrissage DESC
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
                    return $this->getAllNourritureNames();
                }
            } else {
                return 'une erreur est survenue';
            }

        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getAllNourritureNames()
    //Récupère tous les rapports sur le nourrissage avec le dernier rapport veterinaire
    {
        try {
            $bdd = $this->connexionPDO();
            $req = "SELECT consommation_nourriture.id_nourrissage AS id,
            DATE_FORMAT(consommation_nourriture.date_nourrissage, '%d/%m/%Y %H:%i') AS valeur,
            animaux.nom_animal,
            consommation_nourriture.nourriture AS nourriture_donnee,
            consommation_nourriture.quantite AS nourriture_donnee_quantite,
            animaux.etat AS etat_sante,
            rapport_veterinaire.nourriture_propose AS info_veterinaire,
            rapport_veterinaire.quantite_nourriture AS quantite_veterinaire,
            DATE_FORMAT(rapport_veterinaire.date_rapport, '%d/%m/%Y') AS date_rapport_veterinaire_format
            FROM consommation_nourriture
            LEFT JOIN animaux ON consommation_nourriture.animal = animaux.id_animal
            LEFT JOIN (
                SELECT *
                FROM rapport_veterinaire rv
                WHERE rv.date_rapport = (
                           SELECT MAX(date_rapport)
                           FROM rapport_veterinaire
                           WHERE animal = rv.animal
                )
            ) AS rapport_veterinaire ON animaux.id_animal = rapport_veterinaire.animal
            GROUP BY consommation_nourriture.id_nourrissage
            ORDER BY consommation_nourriture.date_nourrissage DESC
            ";

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

    public function getPaginationAllNourritureNames($page, $itemsPerPage)
    //Récupère les rapports de nourrissage triés par page avec le dernier rapport veterinaire
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = "
            SELECT consommation_nourriture.id_nourrissage AS id,
            DATE_FORMAT(consommation_nourriture.date_nourrissage, '%d/%m/%Y %H:%i') AS valeur,
            animaux.nom_animal,
            consommation_nourriture.nourriture AS nourriture_donnee,
            consommation_nourriture.quantite AS nourriture_donnee_quantite,
            animaux.etat AS etat_sante,
            rapport_veterinaire.nourriture_propose AS info_veterinaire,
            rapport_veterinaire.quantite_nourriture AS quantite_veterinaire,
            DATE_FORMAT(rapport_veterinaire.date_rapport, '%d/%m/%Y') AS date_rapport_veterinaire_format
            FROM consommation_nourriture
            LEFT JOIN animaux ON consommation_nourriture.animal = animaux.id_animal
            LEFT JOIN (
                SELECT *
                FROM rapport_veterinaire rv
                WHERE rv.date_rapport = (
                           SELECT MAX(date_rapport)
                           FROM rapport_veterinaire
                           WHERE animal = rv.animal
                )
            ) AS rapport_veterinaire ON animaux.id_animal = rapport_veterinaire.animal
            GROUP BY consommation_nourriture.id_nourrissage
            ORDER BY consommation_nourriture.date_nourrissage DESC
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

    public function getByNourritureId($rapportId)
    //retourne la ligne nourrissage selon l'id
    {
        try {
            $bdd = $this->connexionPDO();
            $req = "
            SELECT consommation_nourriture.id_nourrissage AS id,
            DATE_FORMAT(consommation_nourriture.date_nourrissage, '%d/%m/%Y %H:%i') AS valeur,
            animaux.nom_animal,
            consommation_nourriture.nourriture AS nourriture_donnee,
            consommation_nourriture.quantite AS nourriture_donnee_quantite,
            animaux.etat AS etat_sante,
            rapport_veterinaire.nourriture_propose AS info_veterinaire,
            rapport_veterinaire.quantite_nourriture AS quantite_veterinaire,
            DATE_FORMAT(rapport_veterinaire.date_rapport, '%d/%m/%Y') AS date_rapport_veterinaire_format
            FROM consommation_nourriture
            LEFT JOIN animaux ON consommation_nourriture.animal = animaux.id_animal
            LEFT JOIN (
                SELECT *
                FROM rapport_veterinaire rv
                WHERE rv.date_rapport = (
                           SELECT MAX(date_rapport)
                           FROM rapport_veterinaire
                           WHERE animal = rv.animal
                )
            ) AS rapport_veterinaire ON animaux.id_animal = rapport_veterinaire.animal
            WHERE consommation_nourriture.id_nourrissage = :rapportId
            GROUP BY consommation_nourriture.id_nourrissage";

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

    public function addNourriture($addDate, $addAnimal, $addNou, $addQte)
    // Ajoute un historique de nourriture
    {
        try {
            if (empty($addDate) || empty($addAnimal) || empty($addNou) || empty($addQte)) {
                return "Veuillez fournir toutes les informations nécessaires.";
            }

            $bdd = $this->connexionPDO();
            $req = '
            INSERT INTO consommation_nourriture (date_nourrissage, animal, nourriture, quantite)
            VALUES (:addDate, :addAnimal, :addNou, :addQte)';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);
                    $stmt->bindValue(':addDate', $addDate, PDO::PARAM_STR);
                    $stmt->bindValue(':addAnimal', $addAnimal, PDO::PARAM_STR);
                    $stmt->bindValue(':addNou', $addNou, PDO::PARAM_STR);
                    $stmt->bindValue(':addQte', $addQte, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        return 'Cette nourriture a bien été enregistrée : ' . $addNou;
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

    public function deleteNourriture($rapportId)
    {
        try {
            // Supprime la ligne de nourrissage

            $bdd = $this->connexionPDO();
            $req = '
            DELETE FROM consommation_nourriture
            WHERE id_nourrissage  = :id_nourrissage';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($rapportId)) {
                    $stmt->bindValue(':id_nourrissage', $rapportId, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        return 'La ligne de nourrissage a bien été supprimée ';
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

    public function updateNourriture($raceAction, $addDate, $addAnimal, $addNou, $addQte)
    // modifie une ligne nourrissage
    {
        try {
            $bdd = $this->connexionPDO();

            if (empty($raceAction)) {
                return 'une erreur est survenue';
            } else {
                // Ajout dans la table rapport
                $req = 'UPDATE consommation_nourriture SET ';

                $params = [];

                if (!empty($addDate)) {
                    $req .= 'date_nourrissage = :date_nourrissage, ';
                    $params[':date_nourrissage'] = $addDate;
                }

                if (!empty($addAnimal)) {
                    $req .= 'animal = :animal, ';
                    $params[':animal'] = $addAnimal;
                }

                if (!empty($addNou)) {
                    $req .= 'nourriture = :nourriture, ';
                    $params[':nourriture'] = $addNou;
                }

                if (!empty($addQte)) {
                    $req .= 'quantite   = :quantite, ';
                    $params[':quantite'] = $addQte;
                }

                $req = rtrim($req, ', '); // Supprimer la virgule en trop à la fin de la requête

                $req .= ' WHERE id_nourrissage = :id_nourrissage';
            }

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($raceAction)) {

                    foreach ($params as $paramName => $paramValue) {
                        $stmt->bindValue($paramName, $paramValue, PDO::PARAM_STR);
                    }

                    $stmt->bindValue(':id_nourrissage', $raceAction, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        return "Le nourrissage a été mis à jour avec succès.";
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