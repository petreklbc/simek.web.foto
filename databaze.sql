# 1. Vytvoří soubor databaze.php
@'
<?php
$host = 'localhost';
$db   = 'nazev_vasi_databaze';
$user = 'Ala';
$pass = 'ffff';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
'@ | Out-File -FilePath databaze.php -Encoding utf8

# 2. Vytvoří soubor index.php
@'
<?php
require 'databaze.php';
$zprava = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['muj_soubor'])) {
    $soubor = $_FILES['muj_soubor'];
    if ($soubor['error'] === UPLOAD_ERR_OK) {
        $puvodni_nazev = basename($soubor['name']);
        $unikatni_nazev = time() . '_' . $puvodni_nazev;
        $cilova_složka = 'uploads/';
        $cilova_cesta = $cilova_složka . $unikatni_nazev;
        if (!is_dir($cilova_složka)) { mkdir($cilova_složka, 0755, true); }
        if (move_uploaded_file($soubor['tmp_name'], $cilova_cesta)) {
            $stmt = $pdo->prepare("INSERT INTO soubory (nazev_souboru, cesta) VALUES (?, ?)");
            if ($stmt->execute([$puvodni_nazev, $cilova_cesta])) {
                $zprava = "Soubor byl úspěšně nahrán.";
            } else { $zprava = "Chyba zápisu do DB."; }
        } else { $zprava = "Chyba přesunu souboru."; }
    } else { $zprava = "Chyba uploadu: " . $soubor['error']; }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head><meta charset="UTF-8"><title>Upload</title></head>
<body>
    <h2>Nahrát soubor</h2>
    <?php if (!empty($zprava)) echo "<p>$zprava</p>"; ?>
    <form action="index.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="muj_soubor" required>
        <button type="submit">Odeslat</button>
    </form>
</body>
</html>
'@ | Out-File -FilePath index.php -Encoding utf8

# 3. Vytvoří soubor databaze.sql
@'
CREATE TABLE `soubory` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nazev_souboru` VARCHAR(255) NOT NULL,
  `cesta` VARCHAR(255) NOT NULL,
  `datum_nahrani` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
'@ | Out-File -FilePath databaze.sql -Encoding utf8

# 4. Vytvoří soubor .gitignore
@'
databaze.php
uploads/
'@ | Out-File -FilePath .gitignore -Encoding utf8
