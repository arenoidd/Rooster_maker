<?php
include 'config.php';

// Klas ophalen uit GET-parameter (standaard naar 23SD-A)
$selectedClass = isset($_GET['klas']) ? $_GET['klas'] : '23SD-A';

// Klassenlijst ophalen
$classesQuery = $pdo->query("SELECT id, naam FROM klassen");
$klassen = $classesQuery->fetchAll(PDO::FETCH_ASSOC);

// Rooster ophalen voor geselecteerde klas
$query = "
    SELECT r.*, k.naam AS klas, t.dag, t.starttijd, t.eindtijd, l.naam AS lokaal, v.naam AS vak, d.naam AS docent
    FROM roosters r
    JOIN klassen k ON r.klas_id = k.id
    JOIN tijdsloten t ON r.tijdslot_id = t.id
    JOIN lokalen l ON r.lokaal_id = l.id
    JOIN vakken v ON r.vak_id = v.id
    JOIN docenten d ON v.docent_id = d.id
    WHERE k.naam = :klas_naam
    ORDER BY t.id, t.starttijd;
";
$stmt = $pdo->prepare($query);
$stmt->execute(['klas_naam' => $selectedClass]);
$schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooster Overzicht</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f8ff;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #003366;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #003366;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        select,
        button {
            padding: 10px;
            font-size: 16px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #e6f7ff;
            cursor: pointer;
        }

        button:hover {
            background-color: #b3e0ff;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #003366;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #003366;
            color: white;
        }
    </style>

</head>

<body>
    <div class="container">
        <h1>Rooster Overzicht</h1>
        <form method="GET" action="overzicht.php">
            <label for="klas">Selecteer een klas:</label>
            <select name="klas" id="klas" onchange="this.form.submit()">
                <?php foreach ($klassen as $klas): ?>
                    <option value="<?= $klas['naam'] ?>" <?= $selectedClass == $klas['naam'] ? 'selected' : '' ?>>
                        <?= $klas['naam'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- Knop om rooster te genereren -->
        <form method="POST" action="genereer_rooster.php">
            <button type="submit">Genereer Rooster</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Dag</th>
                    <th>Starttijd</th>
                    <th>Eindtijd</th>
                    <th>Vak</th>
                    <th>Docent</th>
                    <th>Lokaal</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($schedule): ?>
                    <?php foreach ($schedule as $row): ?>
                        <tr>
                            <td><?= $row['dag'] ?></td>
                            <td><?= $row['starttijd'] ?></td>
                            <td><?= $row['eindtijd'] ?></td>
                            <td><?= $row['vak'] ?></td>
                            <td><?= $row['docent'] ?></td>
                            <td><?= $row['lokaal'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Geen rooster beschikbaar voor de geselecteerde klas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>