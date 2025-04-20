<?php
/**
 * @file header.inc.php
 * @author Lisa/Ouardia
 *
 * @brief Ce fichier configure et génère l'en-tête HTML du site :
 * - Gestion dynamique du titre, de la description, et des classes CSS du <body>.
 * - Sélection du thème (style clair "jour" ou sombre "alternatif") via cookie ou paramètre GET.
 * - Encodage du logo en base64 pour affichage sans requête supplémentaire.
 * - Génération dynamique des balises meta, lien CSS, favicon, et du menu de navigation.
 * 
 * PHP version 8.1+
 * 
 *
 * 
 */

require_once("./include/functions.inc.php");
if (!isset($pageTitle)) {
    $pageTitle = "Titre par défaut";
}
if (!isset($pageDescription)) {
    $pageDescription = "Description par défaut";
}
$bodyClass = isset($bodyClass) ? $bodyClass : '';
$validStyles = ['jour', 'alternatif'];

if (isset($_GET['style']) && in_array($_GET['style'], $validStyles)) {
    $style = $_GET['style'];
    // Cookie valable uniquement pour ton espace perso, ex : /~tonlogin/
    setcookie('style', $style, time() + (30 * 24 * 60 * 60), 'https://achabouardia.alwaysdata.net/');
} elseif (isset($_COOKIE['style'])) {
    if (in_array($_COOKIE['style'], $validStyles)) {
        $style = $_COOKIE['style'];
    } else {
        // valeur invalide → supprimer cookie
        setcookie('style', '', time() - 3600, '/');
        $style = 'jour'; // valeur par défaut
    }
} else {
    $style = 'jour';
}
// Définir le chemin de la feuille de style en fonction du paramètre
if ($style === 'alternatif') {
    $styleSheet = 'CSS/styleNuit.css';
} else {
    $styleSheet = 'CSS/style.css';
}
$currentPage = basename($_SERVER['PHP_SELF']);

// Déterminer l'icône à afficher selon le mode actuel
$toggleIcon = ($style === 'alternatif') 
    ? '<svg width="32" height="32" viewBox="0 0 64 64"><circle cx="32" cy="32" r="14" fill="#FFD700"/><line x1="32" y1="4" x2="32" y2="14" stroke="#FFD700" stroke-width="2"/><line x1="32" y1="50" x2="32" y2="60" stroke="#FFD700" stroke-width="2"/><line x1="4" y1="32" x2="14" y2="32" stroke="#FFD700" stroke-width="2"/><line x1="50" y1="32" x2="60" y2="32" stroke="#FFD700" stroke-width="2"/><line x1="12" y1="12" x2="19" y2="19" stroke="#FFD700" stroke-width="2"/><line x1="45" y1="45" x2="52" y2="52" stroke="#FFD700" stroke-width="2"/><line x1="12" y1="52" x2="19" y2="45" stroke="#FFD700" stroke-width="2"/><line x1="45" y1="19" x2="52" y2="12" stroke="#FFD700" stroke-width="2"/></svg>' 
    : '<svg width="32" height="32" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="#FFD700"/><circle cx="70" cy="50" r="30" fill="#f0e9b4"/></svg>';

$toggleMode = ($style === 'alternatif') ? 'jour' : 'alternatif';

//logo en base 64
$imagePath = 'images/logopage.png'; // Chemin vers votre image
$imageData = file_get_contents($imagePath);
$base64Image = base64_encode($imageData);

// Type MIME à adapter selon votre format d'image (image/png ou image/webp)
$mimeType = 'image/png';

// La chaîne à utiliser dans l'attribut src
$srcData = "data:{$mimeType};base64,{$base64Image}";
setCookieAcceptance();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="author" content="Lisa et Ouardia"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>"/>
    <link rel="icon" href="images/faviconMeteo.png"/>
    <link rel="stylesheet" href="<?= htmlspecialchars($styleSheet) ?>"/>
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body class="<?= htmlspecialchars($bodyClass) ?>">
<header>
    <a href="index.php">
       <img src="<?php echo $srcData; ?>" alt="Logo du site"/>
    </a>
    <a href="#" class="scroll-top-btn" title="Retour en haut">
       <span class="arrow-up"></span>
    </a>
    <nav>
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="previsions.php">Prévisions</a></li>
            <li><a href="statistiques.php">Statistiques</a></li>
        </ul>
    </nav>
    <div class="mode-toggle">

    <a href="<?= $currentPage ?>?style=<?= $toggleMode ?>" class="toggle-button <?= $style ?>" aria-label="Passer au mode <?= $toggleMode ?>">            <?= $toggleIcon ?>

        </a>

    </div>
    <?php if (!areCookiesAccepted() && !isset($_COOKIE['cookies_declined'])) : ?>
    <div id="cookie-banner">
    <p><strong>🍪 Cookies</strong><br>
    Ce site utilise des cookies.</p>
    <div class="cookie-buttons">
        <form method="POST">
            <button type="submit" name="accept_cookies">Accepter</button>
        </form>
        <form method="POST">
            <button type="submit" name="decline_cookies">Refuser</button>
        </form>
    </div>
</div>

<?php endif; ?>
</header>