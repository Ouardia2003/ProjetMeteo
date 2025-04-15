<?php
require_once("./include/functions.inc.php");
// Définition du titre et de la description
$pageTitle = "Page Développeur";
$pageDescription = "Page d'interaction avec l'API NASA et les services de géolocalisation.";

// Inclusion des fichiers nécessaires
include("./include/header.inc.php");

// Récupération des données
$ip = getVisitorIP();
$apodData = getProcessedJSON();
$geoDataJSON = getGeoLocationJSON($ip);
$geoDataXML = getGeoLocationXML($ip);
$dateDuJour = getCurrentDate();

$ipKey="3d53fd49177e160c244f13ad56652210";
//output=xml : demande une réponse en format XML.
//file_get_contents($urlWhatisMyip) effectue une requête HTTP GET vers l'API.
//$reponseWhatip contient le contenu XML renvoyé par l'API.
$urlWhatisMyip="https://api.whatismyip.com/ip-address-lookup.php?key=$ipKey&input=$ip&output=xml";
$reponseWhatip=file_get_contents($urlWhatisMyip);
$lines = explode("\n", trim($reponseWhatip));//retourne le tableu en php
?>

<h1>Interactions avec les APIs</h1>

<h2>Image NASA (<?= $dateDuJour ?>)</h2>

<h3><?= htmlspecialchars($apodData['title']) ?></h3> <!-- Affichage du titre de l'image -->

<?php if ($apodData['media_type'] === 'image') : ?>
    <img src="<?= htmlspecialchars($apodData['url']) ?>" alt="Image du jour NASA" width="350"/>
<?php elseif ($apodData['media_type'] === 'video') : ?>
    <iframe width="400" height="400" src="<?= htmlspecialchars($apodData['url']) ?>" allowfullscreen></iframe>
<?php endif; ?>

<p><strong>Description :</strong> <?= htmlspecialchars($apodData['explanation']) ?></p>

<h2>Géolocalisation via JSON</h2>
<p><strong>Ville : </strong><?= htmlspecialchars($geoDataJSON['city'] ?? 'Inconnu') ?></p>
<p><strong>Région :</strong> <?= htmlspecialchars($geoDataJSON['region'] ?? 'Inconnu') ?></p>
<p><strong>Pays :</strong> <?= htmlspecialchars($geoDataJSON['country'] ?? 'Inconnu') ?></p>

<?php
// Extraction des valeurs Latitude et Longitude
if (isset($geoDataJSON['loc'])) {
    [$latitude, $longitude] = explode(',', $geoDataJSON['loc']);
} else {
    $latitude = 'Inconnu';
    $longitude = 'Inconnu';
}
?>

<p><strong>Latitude :</strong> <?= htmlspecialchars($latitude) ?></p>
<p><strong>Longitude : </strong><?= htmlspecialchars($longitude) ?></p>

<h2>Géolocalisation via XML</h2>
<p><strong>Ville : </strong><?= htmlspecialchars($geoDataXML->geoplugin_city ?? 'Inconnu') ?></p>
<p><strong>Région :</strong> <?= htmlspecialchars($geoDataXML->geoplugin_region ?? 'Inconnu') ?></p>
<p><strong>Pays : </strong><?= htmlspecialchars($geoDataXML->geoplugin_countryName ?? 'Inconnu') ?></p>

<h2>Informations sur l'IP publique</h2>
<p><strong>IP Publique :</strong>  <?= $lines[8]?></p>
<p><strong>Code Postal:</strong> <?= $lines[13]?>


<?php include("./include/footer.inc.php"); ?>
