<?php
$host = 'localhost';
$db = 'rooster_maker';
$user = 'root';
$pass = ''; // Vul hier je wachtwoord in als nodig

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Databaseverbinding mislukt: " . $e->getMessage());
}
?>
