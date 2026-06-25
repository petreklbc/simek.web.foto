<?php
// Zapnutí výpisu chyb pro snazší diagnostiku
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $jmeno = isset($_POST['jmeno']) ? trim($_POST['jmeno']) : '';
    $zprava = isset($_POST['zprava']) ? trim($_POST['zprava']) : '';
    
    if ($jmeno === '' || $zprava === '') {
        die("Chyba: Jméno nebo zpráva jsou prázdné. Přišla data: " . json_encode($_POST));
    }
    
    // Očištění textu od zalomení řádků
    $jmeno = str_replace(["\r", "\n", "|"], " ", $jmeno);
    $zprava = str_replace(["\r", "\n", "|"], " ", $zprava);
    
    $radek = $jmeno . "|" . $zprava . "\n";
    $soubor = 'vzkazy.txt';
    
    // Pokus o zápis do souboru
    $zapis = @file_put_contents($soubor, $radek, FILE_APPEND | LOCK_EX);
    
    if ($zapis === false) {
        // Zjistíme, proč zápis selhal (nejčastěji práva zápisu na hostingu)
        $chyba = error_get_last();
        die("Chyba zápisu: Server nemůže zapisovat do souboru vzkazy.txt. Důvod: " . ($chyba['message'] ?? 'Neznámý'));
    }
    
    // Vše proběhlo v pořádku
    echo "OK";
    exit;
} else {
    echo "Chyba: Skript musí být volán metodou POST.";
}
?>
