<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Animaux extends Model
{
    public function getSearchActiveAnimauxNames($search, $page, $itemsPerPage)
    //Récupère les animaux recherchés triés par page
    // si vide retourne tout
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
            SELECT animaux.id_animal AS id,
            animaux.nom_animal AS valeur,
            animaux.etat,
            animaux.race_animal,
            animaux.active_animal,
            races.nom_race AS nom_race,
            animaux.habitat_animal,
            habitats.nom_habitat AS nom_habitat,
            GROUP_CONCAT(images.id_image) AS id_image
            FROM animaux
            LEFT JOIN images_animaux ON animaux.id_animal = images_animaux.id_animal
            LEFT JOIN images ON images_animaux.id_image = images.id_image
            LEFT JOIN races ON animaux.race_animal = races.id_race
            LEFT JOIN habitats ON animaux.habitat_animal = habitats.id_habitat
            WHERE animaux.nom_animal LIKE :search OR
            races.nom_race LIKE :search OR
            habitats.nom_habitat LIKE :search
            AND animaux.active_animal = 1
            GROUP BY
            animaux.id_animal
            ORDER BY animaux.id_animal
            LIMIT :offset, :itemsPerPage';
            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($search) and !empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $animaux = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        // Parcourir les résultats et récupérer les données et types d'image pour chaque animal

                        // Parcourir les résultats et récupérer les données et types d'image pour chaque animal
                        foreach ($animaux as &$animal) {
                            if (isset($animal['id_image']) and !empty($animal['id_image'])) { // si non vide

                                $imageIds = explode(',', $animal['id_image']);
                                $images = [];

                                foreach ($imageIds as $imageId) {
                                    // Requête pour récupérer les données et types d'image pour chaque identifiant d'image
                                    $reqImage = 'SELECT data, type FROM images WHERE id_image = :id_image';
                                    $stmtImage = $bdd->prepare($reqImage);
                                    $stmtImage->bindValue(':id_image', $imageId, PDO::PARAM_INT);
                                    if ($stmtImage->execute()) {
                                        $imageInfo = $stmtImage->fetch(PDO::FETCH_ASSOC);
                                        $images[] = [
                                            'data' => base64_encode($imageInfo['data']),
                                            'type' => $imageInfo['type']
                                        ];
                                    }
                                    $animal['images'] = $images;
                                }
                            }
                        }
                        return $animaux;
                    }
                } else {
                    return $this->getAllAnimauxNames();
                }
            } else {
                return 'une erreur est survenue';
            }

        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getAllActiveAnimauxNames()
    //Récupère tous les animaux
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            SELECT animaux.id_animal AS id,
            animaux.nom_animal AS valeur,
            animaux.etat,
            animaux.race_animal,
            animaux.active_animal,
            races.nom_race AS nom_race,
            animaux.habitat_animal,
            habitats.nom_habitat AS nom_habitat,
            GROUP_CONCAT(images.id_image) AS id_image
            FROM animaux
            LEFT JOIN images_animaux ON animaux.id_animal = images_animaux.id_animal
            LEFT JOIN images ON images_animaux.id_image = images.id_image
            LEFT JOIN races ON animaux.race_animal = races.id_race
            LEFT JOIN habitats ON animaux.habitat_animal = habitats.id_habitat
            WHERE animaux.active_animal = 1
            GROUP BY animaux.id_animal
            ORDER BY animaux.id_animal';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $animaux = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    // Parcourir les résultats et récupérer les données et types d'image pour chaque animal
                    foreach ($animaux as &$animal) {
                        if (isset($animal['id_image']) and !empty($animal['id_image'])) { // si non vide

                            $imageIds = explode(',', $animal['id_image']);
                            $images = [];

                            foreach ($imageIds as $imageId) {
                                // Requête pour récupérer les données et types d'image pour chaque identifiant d'image
                                $reqImage = 'SELECT data, type FROM images WHERE id_image = :id_image';
                                $stmtImage = $bdd->prepare($reqImage);
                                $stmtImage->bindValue(':id_image', $imageId, PDO::PARAM_INT);
                                if ($stmtImage->execute()) {
                                    $imageInfo = $stmtImage->fetch(PDO::FETCH_ASSOC);
                                    $images[] = [
                                        'data' => base64_encode($imageInfo['data']),
                                        'type' => $imageInfo['type']
                                    ];
                                }
                                $animal['images'] = $images;
                            }
                        }
                    }
                    return $animaux;
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getPaginationAllActiveAnimauxNames($page, $itemsPerPage)
    //Récupère les animaux triés par page
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
            SELECT animaux.id_animal AS id,
            animaux.nom_animal AS valeur,
            animaux.etat,
            animaux.race_animal,
            animaux.active_animal,
            races.nom_race AS nom_race,
            animaux.habitat_animal,
            habitats.nom_habitat AS nom_habitat,
            GROUP_CONCAT(images.id_image) AS id_image
            FROM animaux
            LEFT JOIN images_animaux ON animaux.id_animal = images_animaux.id_animal
            LEFT JOIN images ON images_animaux.id_image = images.id_image
            LEFT JOIN races ON animaux.race_animal = races.id_race
            LEFT JOIN habitats ON animaux.habitat_animal = habitats.id_habitat
            WHERE animaux.active_animal = 1
            GROUP BY animaux.id_animal
            ORDER BY animaux.id_animal
            LIMIT :offset, :itemsPerPage';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $animaux = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();

                        // Parcourir les résultats et récupérer les données et types d'image pour chaque animal
                        foreach ($animaux as &$animal) {
                            if (isset($animal['id_image']) and !empty($animal['id_image'])) { // si non vide

                                $imageIds = explode(',', $animal['id_image']);
                                $images = [];

                                foreach ($imageIds as $imageId) {
                                    // Requête pour récupérer les données et types d'image pour chaque identifiant d'image
                                    $reqImage = 'SELECT data, type FROM images WHERE id_image = :id_image';
                                    $stmtImage = $bdd->prepare($reqImage);
                                    $stmtImage->bindValue(':id_image', $imageId, PDO::PARAM_INT);
                                    if ($stmtImage->execute()) {
                                        $imageInfo = $stmtImage->fetch(PDO::FETCH_ASSOC);
                                        $images[] = [
                                            'data' => base64_encode($imageInfo['data']),
                                            'type' => $imageInfo['type']
                                        ];
                                    }
                                    $animal['images'] = $images;
                                }
                            }
                        }
                        return $animaux;
                    }
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getByAnimalId($animalId)
    //retourne l animal selon l'id
    {
        try {

            $bdd = $this->connexionPDO();
            $req = '
            SELECT animaux.id_animal AS id,
            animaux.nom_animal AS valeur,
            animaux.etat,
            animaux.race_animal,
            animaux.active_animal,
            races.nom_race AS nom_race,
            animaux.habitat_animal,
            habitats.nom_habitat AS nom_habitat,
            GROUP_CONCAT(images.id_image) AS id_image
            FROM animaux
            LEFT JOIN images_animaux ON animaux.id_animal = images_animaux.id_animal
            LEFT JOIN images ON images_animaux.id_image = images.id_image
            LEFT JOIN races ON animaux.race_animal = races.id_race
            LEFT JOIN habitats ON animaux.habitat_animal = habitats.id_habitat
            WHERE animaux.id_animal = :animalId
            GROUP BY animaux.id_animal';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($animalId)) {
                    $stmt->bindValue(':animalId', $animalId, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $animal = $stmt->fetch(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        // Parcourir les résultats et récupérer les données et types d'image pour chaque animal

                        if (isset($animal['id_image']) and !empty($animal['id_image'])) { // si non vide

                            $imageIds = explode(',', $animal['id_image']);
                            $images = [];

                            foreach ($imageIds as $imageId) {
                                // Requête pour récupérer les données et types d'image pour chaque identifiant d'image
                                $reqImage = 'SELECT data, type FROM images WHERE id_image = :id_image';
                                $stmtImage = $bdd->prepare($reqImage);
                                $stmtImage->bindValue(':id_image', $imageId, PDO::PARAM_INT);
                                if ($stmtImage->execute()) {
                                    $imageInfo = $stmtImage->fetch(PDO::FETCH_ASSOC);
                                    $images[] = [
                                        'data' => base64_encode($imageInfo['data']),
                                        'type' => $imageInfo['type']
                                    ];
                                }
                                $animal['images'] = $images;
                            }
                        }

                        return $animal;
                    }
                } else {
                    return 'une erreur est survenue';
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getRelatedHabitat($habitatId)
    // Récupère tous les éléments liés à un habitatId
    {
        try {

            // Initialisation de la liste des éléments liés
            $relatedElements = array();

            // Liste des tables avec des clés étrangères vers Country
            $tables = array(
                'animaux' => 'habitat_animal',
            );

            // Boucle sur les tables pour récupérer les éléments liés
            foreach ($tables as $tableName => $foreignKey) {

                $bdd = $this->connexionPDO();
                $req = "SELECT * FROM $tableName WHERE $foreignKey = :habitatId";

                // on teste si la connexion pdo a réussi
                if (is_object($bdd)) {
                    $stmt = $bdd->prepare($req);

                    if (!empty($habitatId) and !empty($habitatId)) {
                        $stmt->bindValue(':habitatId', $habitatId, PDO::PARAM_INT);
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
    public function addAnimal($animauxName, $animauxDesc, $animauxHab, $animauxRace, $animauxImg)
    // Ajoute un animal
    {
        try {
            $bdd = $this->connexionPDO();

            // Commencer la transaction
            $bdd->beginTransaction();

            // Ajout dans la table animaux
            $req = '
            INSERT INTO animaux (nom_animal, etat, race_animal, habitat_animal)
            VALUES (:animauxName, :description, :animauxRace, :animauxHab)';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($animauxName) and !empty($animauxDesc) and !empty($animauxRace) and !empty($animauxHab)) {
                    $stmt->bindValue(':animauxName', $animauxName, PDO::PARAM_STR);
                    $stmt->bindValue(':description', $animauxDesc, PDO::PARAM_STR);
                    $stmt->bindValue(':animauxRace', $animauxRace, PDO::PARAM_INT);
                    $stmt->bindValue(':animauxHab', $animauxHab, PDO::PARAM_INT);


                    if ($stmt->execute()) {
                        $animalId = $bdd->lastInsertId(); // Récupérer l'ID de l'animal inséré

                        if (!empty($animauxImg)) { // Si image n'est pas vide
                            // Insertion des images dans la table images et relation dans la table images_animaux
                            foreach ($animauxImg as $image) {
                                $imageData = $image['data'];
                                $imageType = $image['type'];

                                // Insertion de l'image dans la table images
                                $sqlImage = "INSERT INTO images (data, type) VALUES (:data, :type)";
                                $stmtImage = $bdd->prepare($sqlImage);
                                $stmtImage->bindValue(':data', $imageData, PDO::PARAM_LOB);
                                $stmtImage->bindValue(':type', $imageType, PDO::PARAM_STR);
                                $stmtImage->execute();
                                $imageId = $bdd->lastInsertId(); // Récupérer l'ID de l'image insérée

                                // Relation entre l'animal et l'image dans la table images_animaux
                                $sqlRelation = "INSERT INTO images_animaux (id_animal, id_image) VALUES (:id_animal, :id_image)";
                                $stmtRelation = $bdd->prepare($sqlRelation);
                                $stmtRelation->bindValue(':id_animal', $animalId, PDO::PARAM_INT);
                                $stmtRelation->bindValue(':id_image', $imageId, PDO::PARAM_INT);
                                $stmtRelation->execute();
                            }
                        }
                        // Valider la transaction
                        $bdd->commit();
                        return "Animal ajouté avec succès avec ses images.";
                    }
                } else {
                    return 'une erreur est survenue';
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

    public function ArchiveAnimal($AniAction)
    // Archive l'animal
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            UPDATE animaux
            SET active_animal = 0
            WHERE id_animal  = :AniAction';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($AniAction)) {
                    $stmt->bindValue(':AniAction', $AniAction, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        return 'Cet animal a bien été archivé avec ses photos ';
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
    public function deleteImg($imgId)
    {
        try {
            // Supprime l'image selon l'id

            $bdd = $this->connexionPDO();
            $req = '
            DELETE FROM images
            WHERE id_image  = :imgId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($imgId)) {
                    $stmt->bindValue(':imgId', $imgId, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        return 'cette image a bien été supprimée ';
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

    public function updateHabitat($habAction, $habitatName, $habitatDesc, $habitatImg)
    // modifie un habitat
    {
        try {
            $bdd = $this->connexionPDO();

            // Commencer la transaction
            $bdd->beginTransaction();
            if (empty($habitatName) and empty($habitatDesc)) {
                $req = 'SELECT habitats.id_habitat AS id
                FROM habitats
                WHERE habitats.id_habitat = :id_habitat';
            } else {
                // Ajout dans la table habitats
                $req = '
            UPDATE habitats
            SET ';

                $params = [];

                if (!empty($habitatName)) {
                    $req .= 'nom_habitat = :nom_habitat, ';
                    $params[':nom_habitat'] = $habitatName;
                }

                if (!empty($habitatDesc)) {
                    $req .= 'description = :description, ';
                    $params[':description'] = $habitatDesc;
                }

                $req = rtrim($req, ', '); // Supprimer la virgule en trop à la fin de la requête

                $req .= ' WHERE id_habitat = :id_habitat';
            }

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($habAction)) {

                    foreach ($params as $paramName => $paramValue) {
                        $stmt->bindValue($paramName, $paramValue, PDO::PARAM_STR);
                    }

                    $stmt->bindValue(':id_habitat', $habAction, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $habitatId = $habAction;

                        if (!empty($habitatImg)) { // Si image n'est pas vide
                            // Insertion des images dans la table images et relation dans la table images_habitats
                            foreach ($habitatImg as $image) {
                                $imageData = $image['data'];
                                $imageType = $image['type'];

                                // Insertion de l'image dans la table images
                                $sqlImage = "INSERT INTO images (data, type) VALUES (:data, :type)";
                                $stmtImage = $bdd->prepare($sqlImage);
                                $stmtImage->bindValue(':data', $imageData, PDO::PARAM_LOB);
                                $stmtImage->bindValue(':type', $imageType, PDO::PARAM_STR);
                                $stmtImage->execute();
                                $imageId = $bdd->lastInsertId(); // Récupérer l'ID de l'image insérée

                                // Relation entre l'habitat et l'image dans la table images_habitats
                                $sqlRelation = "INSERT INTO images_habitats (id_habitat, id_image) VALUES (:id_habitat, :id_image)";
                                $stmtRelation = $bdd->prepare($sqlRelation);
                                $stmtRelation->bindValue(':id_habitat', $habitatId, PDO::PARAM_INT);
                                $stmtRelation->bindValue(':id_image', $imageId, PDO::PARAM_INT);
                                $stmtRelation->execute();
                            }
                        }
                        // Valider la transaction
                        $bdd->commit();
                        return "Habitat modifié avec succès avec ses images.";
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