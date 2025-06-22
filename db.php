<?php
$host = "localhost"; // Adresse du serveur MySQL
$dbname = "globaljobs"; // Nom de la base de données
$user = "root"; // Utilisateur MySQL
$password = ""; // Mot de passe MySQL (modifie si nécessaire)

try {
    // Connexion avec PDO
    $conn = new PDO("mysql:host=$host;charset=utf8", $user, $password);
    
    // Définir le mode d'erreur de PDO à Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier si la base existe déjà
    $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn->exec("USE $dbname");

    // Exécuter le script SQL `MySQL.sql` s’il existe
    $sqlFile = __DIR__ . '/MySQL.sql'; // Chemin du fichier SQL
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        $conn->exec($sql);
    } else {
        echo "Le fichier MySQL.sql est introuvable.";
    }

} catch (PDOException $e) {
    die("Échec de connexion à la base de données : " . $e->getMessage());
}
?>