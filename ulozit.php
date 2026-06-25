<?php
// Kontrola, zda přišla správná data metodou POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jmeno']) && isset($_POST['zprava'])) {
    
    // Vyčištění konců řádků z textu, aby se nerozbila struktura souboru
    $jmeno = str_replace(["\r", "\n", "|"], " ", $_POST['jmeno']);
    $zprava = str_replace(["\r", "\n", "|"], " ", $_POST['zprava']);
    
    // Ochrana před prázdnými hodnotami
    if (trim($jmeno) !== '' && trim($zprava) !== '') {
        // Formát uložení: Jméno|Zpráva na jeden řádek
        $radek = $jmeno . "|" . $zprava . "\n";
        
        // Zápis na konec souboru vzkazy.txt (pokud neexistuje, vytvoří se automaticky)
        file_put_contents('vzkazy.txt', $radek, FILE_APPEND | LOCK_EX);
        
        // Vrátíme úspěch
        http_response_code(200);
        echo "Uloženo";
        exit;
    }
}

// Pokud se něco nepovedlo, vrátíme chybu
http_response_code(400);
echo "Špatný požadavek";
?>
