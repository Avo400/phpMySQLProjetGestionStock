
<?php
    $server = "localhost";
    $username = "loli";
    $password = "Zanahari72";
    $db = "magasin_php";

    // Crée la chaine de connexion
    $conn = mysqli_connect($server, $username, $password, $db);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    } 
?>
<?php 
   
    $nomProduitDepot = $_POST["nomProduitDepot"] ?? '';
    $quantiteProduitDepot = $_POST["quantiteProduitDepot"] ?? 0;
    $dateExpirationDepot = $_POST["dateExpirationDepot"] ?? date('Y/m/d h:i:s', time());
    $numeroMagasinDepot = $_POST["numeroMagasinDepot"] ?? 0;

    $nomProduitRetrait = $_POST["nomProduitRetrait"] ?? '';
    $numeroMagasinRetrait = $_POST["numeroMagasinRetrait"] ?? 0;
    $quantiteProduitRetrait = $_POST["quantiteProduitRetrait"]?? 0;
    $dateExpirationRetrait = $_POST["dateExpirationRetrait"] ?? date('Y/m/d h:i:s', time());
?>
<?php
        //enregistrer les depots de produits
        $produitId = 0;
        $produitMagasinId = 0;
        //$actionId = 1 car on se trouve dans le script de traitement du formulaire de dépot. l'action sera donc toujours un dépôt.
        $actionId = 1;
        $dateAction = date('d-m-y h:i:s');
        date_default_timezone_set('Europe/Paris');
        $currentDateTime = date('Y/m/d h:i:s', time());

        if (isset($_POST["enregistrerProduit"]) && !empty($nomProduitDepot) && !empty($quantiteProduitDepot) && !empty($dateExpirationDepot) && !empty($numeroMagasinDepot)) {
            // Insertion du produit dans la table produit
            $queryProduit = "INSERT INTO produit(nom) VALUES ('" . $nomProduitDepot . "');";
            $sql1 = mysqli_query($conn, $queryProduit);
            //Insertion des données dans la table produitMagasin
            $produitId += mysqli_insert_id($conn);
            $queryProduitMagasin = "INSERT INTO produitMagasin(produitId, magasinId, quantite, dateExpiration) VALUES ('" . $produitId . "', '" . $numeroMagasinDepot . "', '" . $quantiteProduitDepot . "', '" . $dateExpirationDepot . "');";
            $sql2 = mysqli_query($conn, $queryProduitMagasin);
            //Insertion des données dans la table historique
            $produitMagasinId += mysqli_insert_id($conn);
            $queryHistorique = "INSERT INTO historique(actionId, dateAction,produitMagasinId) VALUES ('" . $actionId . "', '" . $currentDateTime . "', '" . $produitMagasinId . "');";
            $sql3 = mysqli_query($conn, $queryHistorique);
            if ($sql1 && $sql2 && $sql3) {
                $mess1="<p>Enregistrement effectue avec succes !</p>";
            } else {
                $mess1="<p>Erreur enregistrement d'un produit !</p>";
            }
        }
?>
<?php
     
    //enregistrer les retraits de produits
    if (isset($_POST["enregistrerProduit2"]) && !empty($nomProduitRetrait) && !empty($numeroMagasinRetrait) && !empty($quantiteProduitRetrait) && !empty($dateExpirationRetrait)) {
        $queryRetrait = "UPDATE produitmagasin 
            SET 
            quantite = quantite - '" . $quantiteProduitRetrait . "'
            WHERE produitId=
            (SELECT id FROM produit WHERE nom='" . $nomProduitRetrait . "')
            AND magasinId=(SELECT id FROM magasin WHERE id='" . $numeroMagasinRetrait . "')
            AND dateExpiration='" . $dateExpirationRetrait . "';";

         mysqli_query($conn, $queryRetrait);

    }

?>
<?php
   //supprimer un produit 
    $produitIdSupprime = $_POST['identifiant'] ?? 0;

    if (isset($_POST['supprimerProduit']) && !empty($produitIdSupprime)) {
        //Suppression d'une référence au produit dans la table historique
        $queryHistoriqueSuppr = "DELETE FROM historique WHERE produitmagasinId= 
        (SELECT id FROM produitmagasin WHERE produitId='"  . $produitIdSupprime ."');";
        $sql4 = mysqli_query($conn, $queryHistoriqueSuppr);

        //Suppression d'une référence au produit dans la table produitmagasin
        $queryProduitMagasinSuppr = "DELETE FROM produitmagasin WHERE produitId='"  . $produitIdSupprime ."';";
        $sql5 =mysqli_query($conn, $queryProduitMagasinSuppr);

        //Suppression du produit dans la table produit
        $queryProduitSupprime ="DELETE FROM produit WHERE id= '"  . $produitIdSupprime ."';";
        $sql6 = mysqli_query($conn, $queryProduitSupprime);
        if($sql4 && $sql5 && $sql6){
            $mess1="<p>Suppression d'un produit effectuee avec succes !</p>";
        }
        else {
            $mess1="<p>Erreur suppression!</p>";
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Formulaire d'enregistrements de dépots de produits</title>
</head>
<body>
    <div class="formulaire">
            <h2> Formulaire d'enregistrement des dépôts de produits</h2>
                <h3> DEPOT </h3>
         <form method="POST" action="">
            <fieldset>
            <table>
                <tr>
                    <td> Nom produit</td>
                    <td><input type="text" name="nomProduitDepot"></td>
                </tr> 
                <tr>
                    <td> Quantité produit</td>
                    <td><input type="text" id="quantiteProduitDepot" name="quantiteProduitDepot"></td> 
                </tr>
                <tr>
                    <td> Date d'expiration</td>
                    <td><input type="text" id="dateExpirationDepot" name="dateExpirationDepot"></td> 
                </tr>
                <tr>
                    <td> Numéro magasin</td>
                    <td><input type="text" id="numeroMagasinDepot" name="numeroMagasinDepot"></td> 
                </tr>
                <tr>
                    <td> </td>
                    <td><button type="submit" class="submit" name="enregistrerProduit"> ENREGISTRER </button></td> 
                </tr>
                <tr>
                    <td><input type="number" id="identifiant" name="identifiant" placeholder="identifiant"></td> 
                    <td><button type="submit" class="submit" name="supprimerProduit"> SUPPRIMER</button></td> 
                </tr>
            </table>
        </fieldset>
        </form>
    </div>
    <div class="formulaire">
        <h2> Formulaire d'enregistrement de retraits de produits </h2>
        <h3> RETRAIT </h3>

       <form method="POST" action="">
       <fieldset>
            <table>
                <tr>
                    <td> Nom produit</td>
                    <td><input type="text" name="nomProduitRetrait"></td>
                </tr> 
                <tr>
                    <td> Numéro magasin</td>
                    <td><input type="text"  name="numeroMagasinRetrait"></td> 
                </tr>
                <tr>
                    <td> Quantité produit</td>
                    <td><input type="text"  name="quantiteProduitRetrait"></td> 
                </tr>
                <tr>
                    <td> Date expiration</td>
                    <td><input type="text" name="dateExpirationRetrait"></td> 
                </tr>
                
                <tr>
                    <td> </td>
                    <td><button type="submit" class="submit" name="enregistrerProduit2"> ENREGISTRER </button></td> 
                </tr>
            </table>
        </fieldset>
        </form>
    </div>
       
</body>
</html>

