<?php require_once("./include/functions.inc.php"); 
$pageTitle = "Ma Météo - Statistiques";
$pageDescription = "Consultez les statistiques des villes les plus consultées sur notre site météo.";

// Deux options possibles:
// Option 1: Sauvegarder le SVG dans un fichier
saveCitySVG();

// OU Option 2: Ne rien faire ici et l'afficher directement dans la page

include("./include/header.inc.php"); ?>
<main>
<section class="construction">
    <h2>📊 Statistiques des villes les plus consultées</h2>
    
    <!-- Option 1: Utiliser l'image SVG sauvegardée -->
    <img src="histogram.svg" alt="Histogramme des villes les plus consultées" style="max-width:60%; height:auto;">
    
    <!-- OU Option 2: Afficher le SVG directement -->
    
    <?php displayCityStats(); ?>
</section>

</main>
<?php include("./include/footer.inc.php"); ?>