<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Habitat extends Model
{
    public function getSearchHabitatNames($habitatName, $page, $itemsPerPage)
    //Récupère les habitats recherchés triées par page
    // si vide retourne tout
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
            SELECT habitats.id_habitat AS id,
            habitats.nom_habitat AS valeur,
            habitats.description,
            habitats.avis,
            GROUP_CONCAT(images.id_image) AS id_image
            FROM habitats
            LEFT JOIN images_habitats ON habitats.id_habitat = images_habitats.id_habitat
            LEFT JOIN images ON images_habitats.id_image = images.id_image
            WHERE nom_habitat LIKE :nom_habitat OR
            description LIKE :nom_habitat OR
            avis LIKE :nom_habitat
            GROUP BY
            habitats.id_habitat
            ORDER BY habitats.id_habitat
            LIMIT :offset, :itemsPerPage';
            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($habitatName) and !empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':nom_habitat', '%' . $habitatName . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $habitats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        // Parcourir les résultats et récupérer les données et types d'image pour chaque habitat
                        foreach ($habitats as &$habitat) {
                            if (isset($habitat['id_image']) and !empty($habitat['id_image'])) { // si non vide

                                $imageIds = explode(',', $habitat['id_image']);
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
                                    $habitat['images'] = $images;
                                }
                            }
                        }
                        return $habitats;
                    }
                } else {
                    return $this->getAllHabitatNames();
                }
            } else {
                return 'une erreur est survenue';
            }

        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getAllHabitatNames()
    //Récupère tous les habitats
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            SELECT habitats.id_habitat AS id,
            habitats.nom_habitat AS valeur,
            habitats.description,
            habitats.avis,
            GROUP_CONCAT(images.id_image) AS id_image
            FROM habitats
            LEFT JOIN images_habitats ON habitats.id_habitat = images_habitats.id_habitat
            LEFT JOIN images ON images_habitats.id_image = images.id_image
            GROUP BY
            habitats.id_habitat';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $habitats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    // Parcourir les résultats et récupérer les données et types d'image pour chaque habitat
                    foreach ($habitats as &$habitat) {
                        if (isset($habitat['id_image']) and !empty($habitat['id_image'])) { // si non vide

                            $imageIds = explode(',', $habitat['id_image']);
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
                                $habitat['images'] = $images;
                            }
                        }
                    }
                    return $habitats;
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getPaginationAllHabitatNames($page, $itemsPerPage)
    //Récupère les habitats triés par page
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
            SELECT habitats.id_habitat AS id,
            habitats.nom_habitat AS valeur,
            habitats.description,
            habitats.avis,
            GROUP_CONCAT(images.id_image) AS id_image
            FROM habitats
            LEFT JOIN images_habitats ON habitats.id_habitat = images_habitats.id_habitat
            LEFT JOIN images ON images_habitats.id_image = images.id_image
            GROUP BY
            habitats.id_habitat
            ORDER BY habitats.id_habitat
            LIMIT :offset, :itemsPerPage';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $habitats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();

                        // Parcourir les résultats et récupérer les données et types d'image pour chaque habitat
                        foreach ($habitats as &$habitat) {
                            if (isset($habitat['id_image']) and !empty($habitat['id_image'])) { // si non vide

                                $imageIds = explode(',', $habitat['id_image']);
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
                                    $habitat['images'] = $images;
                                }
                            }
                        }
                        return $habitats;
                    }
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getByHabitatId($habitatId)
    //retourne l habitat selon l'id
    {
        try {

            $bdd = $this->connexionPDO();
            $req = '
            SELECT habitats.id_habitat AS id,
            habitats.nom_habitat AS valeur,
            habitats.description,
            habitats.avis,
            GROUP_CONCAT(images.id_image) AS id_image
            FROM habitats
            LEFT JOIN images_habitats ON habitats.id_habitat = images_habitats.id_habitat
            LEFT JOIN images ON images_habitats.id_image = images.id_image
            WHERE habitats.id_habitat = :habitatId
            GROUP BY
            habitats.id_habitat';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($habitatId)) {
                    $stmt->bindValue(':habitatId', $habitatId, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $habitats = $stmt->fetch(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        // Parcourir les résultats et récupérer les données et types d'image pour chaque habitat

                        if (isset($habitats['id_image']) and !empty($habitats['id_image'])) { // si non vide

                            $imageIds = explode(',', $habitats['id_image']);
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
                                $habitats['images'] = $images;
                            }
                        }

                        return $habitats;
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
    public function addHabitat($habitatName, $habitatDesc, $habitatImg)
    // Ajoute un habitat
    {
        try {
            $bdd = $this->connexionPDO();

            // Commencer la transaction
            $bdd->beginTransaction();

            // Ajout dans la table habitats
            $req = '
            INSERT INTO habitats (nom_habitat, description)
            VALUES (:nom_habitat, :description)';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($habitatName) and !empty($habitatDesc)) {
                    $stmt->bindValue(':nom_habitat', $habitatName, PDO::PARAM_STR);
                    $stmt->bindValue(':description', $habitatDesc, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        $habitatId = $bdd->lastInsertId(); // Récupérer l'ID de l'habitat inséré

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
                        return "Habitat ajouté avec succès avec ses images.";
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

    public function deleteHabitat($habitatId)
    // Supprime l'habitat selon l'id
    {
        try {

            if (!isset($habitatId) or empty($habitatId)) {
                return null;

            } else {
                $bdd = $this->connexionPDO();

                // Début de la transaction
                $bdd->beginTransaction();

                // Supprimer les images associées à cet habitat
                $deleteImagesQuery = 'DELETE FROM images WHERE id_image IN 
                                  (SELECT id_image FROM images_habitats WHERE id_habitat = :habitatId)';
                $stmtDeleteImages = $bdd->prepare($deleteImagesQuery);
                $stmtDeleteImages->bindValue(':habitatId', $habitatId, PDO::PARAM_INT);
                $stmtDeleteImages->execute();

                // Supprimer l'habitat lui-même
                $deleteHabitatQuery = 'DELETE FROM habitats WHERE id_habitat = :habitatId';
                $stmtDeleteHabitat = $bdd->prepare($deleteHabitatQuery);
                $stmtDeleteHabitat->bindValue(':habitatId', $habitatId, PDO::PARAM_INT);
                $stmtDeleteHabitat->execute();

                // Validation de la transaction
                $bdd->commit();

                // Fermeture des curseurs
                $stmtDeleteImages->closeCursor();
                $stmtDeleteHabitat->closeCursor();
                return 'Suppression réussie';
            }

        } catch (Exception $e) {
            $bdd->rollBack();
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
            if (empty($habAction)) {
                return 'une erreur est survenue';
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