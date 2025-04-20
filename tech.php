<?php

/**
 * @file tech.php
 *
 *  @author   Lisa/Ouardia 
 * @brief Page dédiée aux développeurs, présentant des interactions avec des APIs :
 * - API NASA Astronomy Picture of the Day (APOD)
 * - API de géolocalisation via JSON et XML
 * - API whatismyip.com pour la localisation IP détaillée
 *
 * Cette page récupère l'IP du visiteur, ses informations de géolocalisation
 * en formats JSON et XML, l’image ou la vidéo du jour de la NASA, ainsi que
 * des infos avancées via un service IP externe.
 *
 * PHP version 8.1+
 *
 */
require_once("./include/functions.inc.php");
// Définition du titre et de la description
$pageTitle = "Page Développeur";
$pageDescription = "Page d'interaction avec l'API NASA et les services de géolocalisation.";

// Inclusion des fichiers nécessaires
include("./include/header.inc.php");

// Récupération de l'IP du visiteur
$ip = getVisitorIP();

// Données NASA APOD (Astronomy Picture of the Day)
$apodData = getProcessedJSON(); // Doit contenir : title, explanation, url, media_type

// Données de géolocalisation au format JSON
$geoDataJSON = getGeoLocationJSON($ip);

// Données de géolocalisation au format XML
$geoDataXML = getGeoLocationXML($ip);

// Date du jour
$dateDuJour = getCurrentDate();

// Récupération d'infos supplémentaires via whatismyip.com
$ipKey = "3d53fd49177e160c244f13ad56652210";
$urlWhatisMyip = "https://api.whatismyip.com/ip-address-lookup.php?key=$ipKey&input=$ip&output=xml";
$reponseWhatip = file_get_contents($urlWhatisMyip);
$lines = explode("\n", trim($reponseWhatip)); // Chaque ligne contient une info
?>

<h1>Interactions avec les APIs</h1>

<h2>Image NASA (<?= $dateDuJour ?>)</h2>

<h3 lang="en"><?= htmlspecialchars($apodData['title']) ?></h3> <!-- Affichage du titre de l'image -->

<?php if ($apodData['media_type'] === 'image') : ?>
    <img src="<?= htmlspecialchars($apodData['url']) ?>" alt="Image du jour NASA" width="350"/>
<?php elseif ($apodData['media_type'] === 'video') : ?>
    <iframe width="400" height="400" src="<?= htmlspecialchars($apodData['url']) ?>" allowfullscreen></iframe>
<?php endif; ?>

<p lang="en"><strong>Description :</strong> <?= htmlspecialchars($apodData['explanation']) ?></p>

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
<p><strong>IP Publique :</strong> <?= isset($lines[8]) ? htmlspecialchars(strip_tags($lines[8])) : 'Non disponible' ?></p>
<p><strong>Code Postal:</strong> <?= isset($lines[13]) ? htmlspecialchars(strip_tags($lines[13])) : 'Non disponible' ?></p>



<?php include("./include/footer.inc.php"); ?>