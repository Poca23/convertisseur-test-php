<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fichier'])) {
    $format = $_POST['format'];
    $extension = pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION);
    if (in_array($format, ['json', 'xml', 'csv'])) {
        $temp = sys_get_temp_dir() . '/' . uniqid() . '.' . $extension;
        move_uploaded_file($_FILES['fichier']['tmp_name'], $temp);
        $resultat = shell_exec('php console ' . escapeshellarg($temp) . ' ' . $format . ' 2>&1');
        unlink($temp);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Convertisseur</title>
</head>

<body>
    <h1>Convertisseur de fichiers</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="fichier" required>
        <select name="format">
            <?php foreach (['json', 'xml', 'csv'] as $f): ?>
                <option value="<?= $f ?>" <?= ($_POST['format'] ?? '') === $f ? 'selected' : '' ?>><?= strtoupper($f) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Convertir</button>
    </form>
    <?php if (isset($resultat)): ?>
        <h2>Résultat</h2>
        <pre><?= htmlspecialchars($resultat) ?></pre>
    <?php endif; ?>
</body>

</html>