<?php
/**
 * @file statistiques.php
 * 
 *  @author   Lisa/Ouardia 
 * @brief Cette page affiche les statistiques de fréquentation des villes les plus consultées sur le site "Ma Météo". 
 * Le graphique est généré dynamiquement en SVG via PHP et affiché directement sur la page.
 *
 * 
 * PHP version 8.1+
 */

require_once("./include/functions.inc.php"); // Inclusion des fonctions liées aux statistiques

// Métadonnées de la page
$pageTitle = "Statistiques - Ma Météo";
$pageDescription = "Consultez les statistiques des villes les plus consultées sur notre site météo.";

// Option 1 : Générer et sauvegarder le graphique SVG dans un fichier
saveCitySVG();

// Option 2 (commentée ici) : Ne rien faire maintenant et afficher directement plus bas

// Inclusion de l'en-tête HTML
include("./include/header.inc.php");
?>

<main>
<section class="construction">
    <h2>📊 Statistiques des villes les plus consultées</h2>
    
    <!-- Affichage de l'image SVG préalablement sauvegardée -->
    <img src="histogram.svg" alt="Histogramme des villes les plus consultées" style="max-width:60%; height:auto;"/>
    
    <!-- Affichage dynamique direct des statistiques (SVG inline ou autre représentation) -->
    <?php displayCityStats(); ?>
</section>
</main>

<?php
// Inclusion du pied de page HTML
include("./include/footer.inc.php");
?>