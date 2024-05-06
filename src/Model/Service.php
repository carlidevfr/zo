<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Service extends Model
{
    public function getSearchServiceNames($Name, $page, $itemsPerPage)
    //Récupère les services recherchés triés par page
    // si vide retourne tout
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
            SELECT id_service AS id, titre AS valeur,
            description
            FROM services
            WHERE titre LIKE :nom OR
            description LIKE :nom
            ORDER BY id_service
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
                    return $this->getAllServiceNames();
                }
            } else {
                return 'une erreur est survenue';
            }

        } catch (Exception $e) {
            $this->logError($e);
        }
    }

    public function getAllServiceNames()
    //Récupère tous les services
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            SELECT id_service AS id, titre AS valeur,
            description
            FROM services
            ORDER BY id_service';

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

    public function getPaginationAllServiceNames($page, $itemsPerPage)
    //Récupère les services triées par page
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
            SELECT id_service AS id, titre AS valeur,
            description
            FROM services
            ORDER BY id_service
            LIMIT :offset, :itemsPerPage';

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

    public function getByServiceId($id_service)
    //retourne le service selon l'id
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            SELECT id_service AS id, titre AS valeur,
            description
            FROM services
            WHERE id_service  = :id_service';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($id_service)) {
                    $stmt->bindValue(':id_service', $id_service, PDO::PARAM_INT);

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

    public function addService($serviceName, $serviceDesc)
    // Ajoute un service
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            INSERT INTO services (titre, description)
            VALUES (:serviceName, :serviceDesc)';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($serviceName) and !empty($serviceDesc)) {
                    $stmt->bindValue(':serviceName', $serviceName, PDO::PARAM_STR);
                    $stmt->bindValue(':serviceDesc', $serviceDesc, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        return 'Le service suivant a bien été ajoutée : ' . $serviceName;
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

    public function deleteService($Id)
    {
        try {
            // Supprime le service selon l'id

            $bdd = $this->connexionPDO();
            $req = '
            DELETE FROM services
            WHERE id_service  = :Id';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($Id)) {
                    $stmt->bindValue(':Id', $Id, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'Le service a bien été supprimé ';
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

    public function updateService($Id, $newName, $desc)
    // Modifie le service selon l'id
    {
        try {
            $bdd = $this->connexionPDO();

            if (empty($Id)) {
                return 'une erreur est survenue';
            } else {
                // Ajout dans la table rapport
                $req = 'UPDATE services SET ';

                $params = [];

                if (!empty($newName)) {
                    $req .= 'titre = :titre, ';
                    $params[':titre'] = $newName;
                }

                if (!empty($desc)) {
                    $req .= 'description = :description, ';
                    $params[':description'] = $desc;
                }

                $req = rtrim($req, ', '); // Supprimer la virgule en trop à la fin de la requête

                $req .= ' WHERE id_service = :id_service';
            }

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($Id)) {

                    foreach ($params as $paramName => $paramValue) {
                        $stmt->bindValue($paramName, $paramValue, PDO::PARAM_STR);
                    }

                    $stmt->bindValue(':id_service', $Id, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        return "Le service a été mis à jour avec succès.";
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