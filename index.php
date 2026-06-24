<?php
// Načtení připojení k databázi ze souboru databaze.php
require_once 'databaze.php';

$zprava = '';

// Kontrola, zda uživatel odeslal formulář
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['muj_soubor'])) {
    $soubor = $_FILES['muj_soubor'];
    
    // Kontrola, zda při nahrávání nedošlo k chybě
    if ($soubor['error'] === UPLOAD_ERR_OK) {
        $puvodni_nazev = basename($soubor['name']);
        
        // Vytvoření unikátního názvu pomocí časové značky (time)
        $unikatni_nazev = time() . '_' . $puvodni_nazev;
        $cilova_složka = 'uploads/';
        $cilova_cesta = $cilova_složka . $unikatni_nazev;

        // Pokud složka pro nahrávání neexistuje, skript ji sám vytvoří
        if (!is_dir($cilova_složka)) {
            mkdir($cilova_složka, 0755, true);
        }

        // Přesun souboru z dočasného úložiště na serveru do cílové složky
        if (move_uploaded_file($soubor['tmp_name'], $cilova_cesta)) {
            
            // Příprava SQL dotazu pomocí Prepared Statement (ochrana před SQL Injection)
            $stmt = $pdo->prepare("INSERT INTO soubory (nazev_souboru, cesta) VALUES (?, ?)");
            
            // Spuštění dotazu s parametry
            if ($stmt->execute([$puvodni_nazev, $cilova_cesta])) {
                $zprava = "Soubor byl úspěšně nahrán a uložen do databáze.";
            } else {
                $zprava = "Soubor byl nahrán, ale nepodařilo se uložit data do databáze.";
            }
        } else {
            $zprava = "Při přesunu souboru do cílové složky došlo k chybě.";
        }
    } else {
        $zprava = "Chyba při nahrávání souboru. Kód chyby: " . $soubor['error'];
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nahrávání souborů</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f9f9f9; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 400px; }
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 4px; background-color: #e3f2fd; color: #0d47a1; }
        input[type="file"] { display: block; margin-bottom: 15px; }
        button { background-color: #007bff; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="container">
    <h2>Nahrát soubor</h2>
    
    <!-- Zobrazení výsledné zprávy uživateli -->
    <?php if (!empty($zprava)): ?>
        <div class="alert"><?php echo htmlspecialchars($zprava); ?></div>
    <?php endif; ?>

    <!-- Atribut enctype="multipart/form-data" je naprosto klíčový pro přenos souborů -->
    <form action="index.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="muj_soubor" required>
        <button type="submit">Odeslat soubor</button>
    </form>
</div>

</body>
</html>
