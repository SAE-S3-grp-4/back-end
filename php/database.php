<?php

require_once('constantes.php');

function dbConnect()
{
    try {
        $db = new PDO(
            'mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME . ';charset=utf8',
            DB_USER,
            DB_PASSWORD
        );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $exception) {
        error_log('Connection error: ' . $exception->getMessage());
        return false;
    }
    return $db;
}



function dbRequestUser($db)
{
  try {
    $request = 'SELECT * FROM MEMBRE';
    $statement = $db->prepare($request);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $exception) {
    error_log('Request error: ' . $exception->getMessage());
    return false;
  }
  return $result;
}

function dbRequestEvent($db)
{
  try {
    $request = 'SELECT * FROM EVENEMENT';
    $statement = $db->prepare($request);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $exception) {
    error_log('Request error: ' . $exception->getMessage());
    return false;
  }
  return $result;
}

function dbRequestProduct($db)
{
  try {
    $request = 'SELECT * FROM PRODUIT';
    $statement = $db->prepare($request);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $exception) {
    error_log('Request error: ' . $exception->getMessage());
    return false;
  }
  return $result;
}

function dbAddProduct($db, $nom, $desc, $img, $prix, $stock)
{
    try {
        $request = 'INSERT INTO PRODUIT(Nom_Produit, Description_Produit, Img_Produit, Prix_Produit, Stock_Produit) VALUES(:Nom_Produit, :Description_Produit, :Img_Produit, :Prix_Produit, :Stock_Produit)';
        $statement = $db->prepare($request);
        $statement->bindParam(':Nom_Produit', $nom, PDO::PARAM_STR, 50);
        $statement->bindParam(':Description_Produit', $desc, PDO::PARAM_STR, 200);
        $statement->bindParam(':Img_Produit', $img, PDO::PARAM_STR, 200);
        $statement->bindParam(':Prix_Produit', $prix, PDO::PARAM_INT, 10);
        $statement->bindParam(':Stock_Produit', $stock, PDO::PARAM_INT, 10);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error:' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbAddEvent($db, $nom, $desc, $date, $prix, $nbPlace, $dateFinInscription)
{
    try {
        $request = 'INSERT INTO EVENEMENT (Nom_Event, Description_Event, Date_Event, Prix_Event, Nb_Place_Event, Date_Fin_Inscription) 
                    VALUES (:Nom_Event, :Description_Event, :Date_Event, :Prix_Event, :Nb_Place_Event, :Date_Fin_Inscription)';
        $statement = $db->prepare($request);
        $statement->bindParam(':Nom_Event', $nom, PDO::PARAM_STR);
        $statement->bindParam(':Description_Event', $desc, PDO::PARAM_STR);
        $statement->bindParam(':Date_Event', $date, PDO::PARAM_STR);
        $statement->bindParam(':Prix_Event', $prix, PDO::PARAM_INT);
        $statement->bindParam(':Nb_Place_Event', $nbPlace, PDO::PARAM_INT);
        $statement->bindParam(':Date_Fin_Inscription', $dateFinInscription, PDO::PARAM_STR);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbDeleteProduct($db, $id)
{
    try {
        // Update the status of related orders to "Produit supprimé"
        $updateOrderStatus = 'UPDATE COMMANDE SET Statut_Commande = "Produit supprimé" WHERE Id_Commande IN (SELECT Id_Commande FROM BON_DE_COMMANDE WHERE Id_Produit = :id)';
        $statement = $db->prepare($updateOrderStatus);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        // Delete related order slips
        $deleteOrderSlips = 'DELETE FROM BON_DE_COMMANDE WHERE Id_Produit = :id';
        $statement = $db->prepare($deleteOrderSlips);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        // Delete the product itself
        $deleteProduct = 'DELETE FROM PRODUIT WHERE Id_Produit = :id';
        $statement = $db->prepare($deleteProduct);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbDeleteEvent($db, $id)
{
    try {
        // First, update the status of the related commands to 'Annulée'
        $updateCommandStatus = 'UPDATE COMMANDE SET Statut_Commande = "Annulée" WHERE Id_Commande IN (SELECT Id_Commande FROM INSCRIPTION WHERE Id_Event = :id)';
        $statement = $db->prepare($updateCommandStatus);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        // Then, delete the inscriptions related to the event
        $deleteInscriptions = 'DELETE FROM INSCRIPTION WHERE Id_Event = :id';
        $statement = $db->prepare($deleteInscriptions);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        // Finally, delete the event itself
        $deleteEvent = 'DELETE FROM EVENEMENT WHERE Id_Event = :id';
        $statement = $db->prepare($deleteEvent);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbModifyProduct($db, $id, $nom, $desc, $img, $prix, $stock)
{
    try {
        if ($img) {
            $request = 'UPDATE PRODUIT SET Nom_Produit = :Nom_Produit, Description_Produit = :Description_Produit, Img_Produit = :Img_Produit, Prix_Produit = :Prix_Produit, Stock_Produit = :Stock_Produit WHERE Id_Produit = :Id_Produit';
            echo $request;
            $statement = $db->prepare($request);
            $statement->bindParam(':Img_Produit', $img, PDO::PARAM_STR, 250);
        } else {
            $request = 'UPDATE PRODUIT SET Nom_Produit = :Nom_Produit, Description_Produit = :Description_Produit, Prix_Produit = :Prix_Produit, Stock_Produit = :Stock_Produit WHERE Id_Produit = :Id_Produit';
            $statement = $db->prepare($request);
        }
        $statement->bindParam(':Id_Produit', $id, PDO::PARAM_INT);
        $statement->bindParam(':Nom_Produit', $nom, PDO::PARAM_STR, 50);
        $statement->bindParam(':Description_Produit', $desc, PDO::PARAM_STR, 150);
        $statement->bindParam(':Prix_Produit', $prix, PDO::PARAM_STR, 15);
        $statement->bindParam(':Stock_Produit', $stock, PDO::PARAM_INT);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbRequestProductById($db, $id)
{
    try {
        $request = 'SELECT * FROM PRODUIT WHERE Id_Produit = :Id_Produit';
        $statement = $db->prepare($request);
        $statement->bindParam(':Id_Produit', $id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return $result;
}

function dbModifyEvent($db, $id, $nom, $desc, $date, $prix, $nbPlace, $dateFinInscription)
{
    try {
        $request = 'UPDATE EVENEMENT SET Nom_Event = :Nom_Event, Description_Event = :Description_Event, Date_Event = :Date_Event, Prix_Event = :Prix_Event, Nb_Place_Event = :Nb_Place_Event, Date_Fin_Inscription = :Date_Fin_Inscription WHERE Id_Event = :Id_Event';
        $statement = $db->prepare($request);
        $statement->bindParam(':Id_Event', $id, PDO::PARAM_INT);
        $statement->bindParam(':Nom_Event', $nom, PDO::PARAM_STR);
        $statement->bindParam(':Description_Event', $desc, PDO::PARAM_STR);
        $statement->bindParam(':Date_Event', $date, PDO::PARAM_STR);
        $statement->bindParam(':Prix_Event', $prix, PDO::PARAM_INT);
        $statement->bindParam(':Nb_Place_Event', $nbPlace, PDO::PARAM_INT);
        $statement->bindParam(':Date_Fin_Inscription', $dateFinInscription, PDO::PARAM_STR);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbRequestEventById($db, $id)
{
    try {
        $request = 'SELECT * FROM EVENEMENT WHERE Id_Event = :Id_Event';
        $statement = $db->prepare($request);
        $statement->bindParam(':Id_Event', $id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return $result;
}

function dbLoginUser($db, $username, $password)
{
    try {
        $request = 'SELECT * FROM MEMBRE WHERE Nom_Membre = :username OR Mail_Membre = :username';
        $statement = $db->prepare($request);
        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['Mdp_Membre'])) {
            return $user;
        } else {
            return false;
        }
    } catch (PDOException $exception) {
        error_log('Login error: ' . $exception->getMessage());
        return false;
    }
}

function dbRegisterUser($db, $name, $email, $password, $group)
{
    try {
        $request = 'INSERT INTO MEMBRE (Nom_Membre, Mail_Membre, Mdp_Membre, Grp_Membre, Id_Role) VALUES (:name, :email, :password, :group, 1)';
        $statement = $db->prepare($request);
        $statement->bindParam(':name', $name, PDO::PARAM_STR);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->bindParam(':password', $password, PDO::PARAM_STR);
        $statement->bindParam(':group', $group, PDO::PARAM_STR);
        $statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log('Registration error: ' . $exception->getMessage());
        return false;
    }
}

function dbUserExists($db, $name, $email)
{
    try {
        $request = 'SELECT COUNT(*) FROM MEMBRE WHERE Nom_Membre = :name OR Mail_Membre = :email';
        $statement = $db->prepare($request);
        $statement->bindParam(':name', $name, PDO::PARAM_STR);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->execute();
        $count = $statement->fetchColumn();
        return $count > 0;
    } catch (PDOException $exception) {
        error_log('User exists check error: ' . $exception->getMessage());
        return false;
    }
}

