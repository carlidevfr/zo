<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Race extends Model
{
    public function getSearchRaceNames($raceName, $page, $itemsPerPage)
    //Récupère les races recherchées triées par page
    // si vide retourne tout
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
        SELECT id_race AS id, nom_race AS valeur
        FROM races
        WHERE nom_race LIKE :nom_race
        ORDER BY id_race
        LIMIT :offset, :itemsPerPage';
            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($raceName) and !empty ($page) and !empty ($itemsPerPage)) {
                    $stmt->bindValue(':nom_race', '%' . $raceName . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $races = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $races;
                    }
                } else {
                    return $this->getAllRaceNames();
                }
            } else {
                return 'une erreur est survenue';
            }

        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getAllRaceNames()
    //Récupère toutes les races
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            SELECT id_race AS id, nom_race AS valeur
            FROM races';

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

    public function getPaginationAllRaceNames($page, $itemsPerPage)
    //Récupère les races triées par page
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
            SELECT id_race AS id, nom_race AS valeur
            FROM races
            ORDER BY id_race
            LIMIT :offset, :itemsPerPage';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty ($page) and !empty ($itemsPerPage)) {
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

    public function getByRaceId($raceId)
    //retourne la race selon l'id
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
        SELECT id_race AS id, nom_race AS valeur
        FROM races
        WHERE id_race  = :raceId';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty ($raceId)) {
                    $stmt->bindValue(':raceId', $raceId, PDO::PARAM_INT);

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

    public function getRelatedRace($raceId)
    // Récupère tous les éléments liés à un raceId
    {
        try {

            // Initialisation de la liste des éléments liés
            $relatedElements = array();

            // Liste des tables avec des clés étrangères vers Country
            $tables = array(
                'animaux' => 'race_animal',
            );

            // Boucle sur les tables pour récupérer les éléments liés
            foreach ($tables as $tableName => $foreignKey) {

                $bdd = $this->connexionPDO();
                $req = "SELECT * FROM $tableName WHERE $foreignKey = :raceId";

                // on teste si la connexion pdo a réussi
                if (is_object($bdd)) {
                    $stmt = $bdd->prepare($req);

                    if (!empty($raceId) and !empty($raceId)) {
                        $stmt->bindValue(':raceId', $raceId, PDO::PARAM_INT);
                        if ($stmt->execute()) {
                            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $stmt->closeCursor();

                            // Ajout des résultats à la liste
                            $relatedElements[$tableName] = $results;
                        } else {
                            return 'une erreur est survenue';
                        }
                    }
                } else {
                    return 'une erreur est survenue';
                }
            }

            // Retourne la liste des éléments liés
            return $relatedElements;

        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function addRace($raceName)
    // Ajoute une race
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            INSERT INTO races (nom_race)
            VALUES (:raceName)';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty ($raceName)) {
                    $stmt->bindValue(':raceName', $raceName, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'La race suivante a bien été ajoutée : ' . $raceName;
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

    public function deleteRace($raceId)
    {
        try {
            // Supprime la race selon l'id

            $bdd = $this->connexionPDO();
            $req = '
            DELETE FROM races
            WHERE id_race  = :raceId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($raceId)) {
                    $stmt->bindValue(':raceId', $raceId, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'La race a bien été supprimée ';
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

    public function updateRace($raceId, $newName)
    // Modifie la race selon l'id
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            UPDATE races
            SET nom_race = :newName
            WHERE id_race  = :raceId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($raceId) and !empty ($newName)) {
                    $stmt->bindValue(':raceId', $raceId, PDO::PARAM_INT);
                    $stmt->bindValue(':newName', $newName, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'La race a bien été modifiée : ' . $newName;
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