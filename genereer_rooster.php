<?php
include 'config.php';

// Functie om lokaal met voorkeur te selecteren
function getVoorkeursLokaal($vak, $lokalen)
{
    $mogelijkheden = [];

    foreach ($lokalen as $lokaal) {
        if (
            (stripos($vak['naam'], 'praktijk') !== false && stripos($lokaal['type'], 'praktijk') !== false) ||
            (stripos($vak['naam'], 'vak') !== false && stripos($lokaal['type'], 'vaklokaal') !== false)
        ) {
            $mogelijkheden[] = $lokaal;
        }
    }

    // Controleer of er mogelijkheden zijn
    if (!empty($mogelijkheden)) {
        return $mogelijkheden[array_rand($mogelijkheden)];
    }

    // Als er geen mogelijkheden zijn, kies een standaard lokaal (indien beschikbaar)
    if (!empty($lokalen)) {
        return $lokalen[0];
    }

    // Als er helemaal geen lokalen zijn, geef null terug
    return null;
}

function bestaatLes($pdo, $klas_id, $tijdslot_id, $lokaal_id, $vak_id)
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM roosters 
        WHERE klas_id = :klas_id AND tijdslot_id = :tijdslot_id AND lokaal_id = :lokaal_id AND vak_id = :vak_id
    ");
    $stmt->execute([
        'klas_id' => $klas_id,
        'tijdslot_id' => $tijdslot_id,
        'lokaal_id' => $lokaal_id,
        'vak_id' => $vak_id
    ]);
    return $stmt->fetchColumn() > 0;
}

function tijdslotBezet($pdo, $tijdslot_id, $lokaal_id)
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM roosters 
        WHERE tijdslot_id = :tijdslot_id AND lokaal_id = :lokaal_id
    ");
    $stmt->execute([
        'tijdslot_id' => $tijdslot_id,
        'lokaal_id' => $lokaal_id
    ]);
    return $stmt->fetchColumn() > 0;
}

// Verwijder bestaande roosters
$pdo->exec("DELETE FROM roosters");

// Data ophalen
$klassen = $pdo->query("SELECT id, naam FROM klassen")->fetchAll(PDO::FETCH_ASSOC);
$vakken = $pdo->query("SELECT id, naam, uren_per_week FROM vakken")->fetchAll(PDO::FETCH_ASSOC);
$lokalen = $pdo->query("SELECT id, naam, type FROM lokalen")->fetchAll(PDO::FETCH_ASSOC);
$tijdsloten = $pdo->query("SELECT id, dag, starttijd, eindtijd FROM tijdsloten ORDER BY dag, starttijd")->fetchAll(PDO::FETCH_ASSOC);

// Maximum uren per week
$maxUrenPerWeek = 22;

// Rooster genereren
foreach ($klassen as $klas) {
    $urenTotaal = 0;

    foreach ($vakken as $vak) {
        $urenVak = $vak['uren_per_week'];

        while ($urenVak > 0 && $urenTotaal < $maxUrenPerWeek) {
            foreach ($tijdsloten as $tijdslot) {
                $voorkeursLokaal = getVoorkeursLokaal($vak, $lokalen);

                if ($voorkeursLokaal === null) {
                    // Geen geldig lokaal, sla dit tijdslot over
                    continue;
                }

                if (
                    !tijdslotBezet($pdo, $tijdslot['id'], $voorkeursLokaal['id']) &&
                    !bestaatLes($pdo, $klas['id'], $tijdslot['id'], $voorkeursLokaal['id'], $vak['id'])
                ) {

                    $stmt = $pdo->prepare("
                        INSERT INTO roosters (klas_id, tijdslot_id, lokaal_id, vak_id)
                        VALUES (:klas_id, :tijdslot_id, :lokaal_id, :vak_id)
                    ");
                    $stmt->execute([
                        'klas_id' => $klas['id'],
                        'tijdslot_id' => $tijdslot['id'],
                        'lokaal_id' => $voorkeursLokaal['id'],
                        'vak_id' => $vak['id']
                    ]);

                    $urenVak--;
                    $urenTotaal++;
                }

                if ($urenVak <= 0 || $urenTotaal >= $maxUrenPerWeek) {
                    break; // Stop als limiet bereikt is
                }
            }
        }
    }
}



// Redirect
header("Location: overzicht.php");
exit;
