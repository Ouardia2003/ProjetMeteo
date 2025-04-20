<?php
/**
 * @file previsions.php
 *  @author   Lisa/Ouardia 
 * @brief Fichier principal pour l'affichage des prévisions météo par région, département et ville.
 *
 * Ce script :
 * - Charge les données depuis des fichiers CSV (régions, départements, villes)
 * - Gère la navigation entre région, département et ville
 * - Affiche la météo selon la ville sélectionnée
 * - Mémorise la dernière ville consultée avec cookies et CSV
 * PHP version 8.1+
 * 
 */

 // Inclusion des fonctions et initialisation de la mise en tampon de sortie

 require_once("./include/functions.inc.php");
 ob_start();
 // Définition du titre de la page
 $pageTitle = "Ma Météo - Prévisions Météo & Climat";
 // Description de la page
 $pageDescription = "Choisissez un département après avoir sélectionné votre région puis une ville pour voir la météo détaillée.";
 // Inclusion de l'en-tête de la page
 include("./include/header.inc.php");
 // Lecture des données depuis les fichiers CSV
 $regions = readCSV("v_region_2024.csv");
 $departements = readCSV("v_departement_2024.csv");
 $villes = readCSV_villes("v_ville_2024.csv");
 
 // Récupération des valeurs sélectionnées
 $selectedRegion = $_GET['region'] ?? null;
 $selectedDepartement = $_GET['departement'] ?? null;
 $selectedVille = $_GET['ville'] ?? null;
 
 // Vérifie que le paramètre "ville" est bien présent dans l'URL
 if (isset($_GET['ville']) && !empty($_GET['ville'])) {
     $villeCode = $_GET['ville'];
 
     // Enregistre la visite et le cookie
     logCityVisit($villeCode);
     setCityCookie($villeCode);
 
     // Récupérer les noms de villes
     $cityNames = loadCityNames();
     $villeNom = $cityNames[$villeCode] ?? "Ville inconnue";
 // Affichage des prévisions météo détaillées pour la ville sélectionnée
     // Vous pouvez implémenter cette partie en fonction de votre logique spécifique.
 
 }
 // Vérification si l'utilisateur arrive sur la page d'accueil sans paramètres
 if (empty($_GET['region']) && empty($_GET['departement']) && empty($_GET['ville'])) {
     // Récupérer la dernière ville consultée depuis le cookie
     $lastCityCode = getLastVisitedCity();
     
     if ($lastCityCode) {
         // Trouver les informations de la ville pour la redirection
         $villeInfo = getCityInfoByCode($lastCityCode);
         
         if ($villeInfo) {
             // Rediriger vers la même page avec les paramètres de la dernière ville consultée
             header("Location: previsions.php?region=" . urlencode($villeInfo['region']) . 
                   "&departement=" . urlencode($villeInfo['departement']) . 
                   "&ville=" . urlencode($lastCityCode));
             exit;
         }
     }
 }
 
 ?>
 <!-- Affichage de la dernière ville consultée -->
 <div class="last-consultation">
     <?php
     // Récupérer la dernière ville consultée depuis le cookie
     $lastCityCode = $_GET['ville'] ?? getLastVisitedCity();
     
     if ($lastCityCode) {
         // Charger les noms des villes pour trouver le nom correspondant au code
         $cityNames = loadCityNames();
         $lastCityName = $cityNames[$lastCityCode] ?? "Ville inconnue";
         
         // Récupérer les données de consultation dans le fichier CSV
         $lastConsultationData = null;
         if (file_exists('villes_consultees.csv') && ($handle = fopen('villes_consultees.csv', 'r')) !== false) {
             $lastConsultation = [];
             
             while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                 if ($data[0] === $lastCityCode) {
                     $lastConsultation = $data;
                 }
             }
             fclose($handle);
             
             if (!empty($lastConsultation)) {
                 $lastConsultationDate = $lastConsultation[1];
                 echo "<p><strong>Dernière consultation :</strong> " . htmlspecialchars($lastCityName) . " (" . htmlspecialchars($lastConsultationDate) . ")</p>";
             }
         }
     }
     ?>
     <!-- 📍 Carte de sélection des régions -->
 </div>
<h2>🗺 Cliquez sur une région de la carte</h2>
<div class="map-container">
<figure>
<img src="images/mapFrance.png" usemap="#image-map" alt="Carte des régions de France"/>
<figcaption>🗺 Cliquez sur une région pour commencer</figcaption>
</figure>
</div>
<!-- 🗺 Définition des zones cliquables de la carte -->
<map name="image-map">
    <area alt="Hauts-de-France" title="Hauts-de-France" href="previsions.php?region=Hauts-de-France" coords="225,18,237,15,257,6,260,23,281,24,288,36,294,39,301,44,313,40,320,49,316,59,305,70,309,77,297,93,292,97,290,107,289,114,279,108,269,106,261,106,251,106,237,101,235,87,231,64,224,57" shape="poly"/>
    <area alt="Grand Est" title="Grand Est" href="previsions.php?region=Grand%20Est" coords="327,73,321,78,317,89,313,100,306,106,303,118,299,130,298,138,298,148,301,156,309,165,318,162,328,157,336,161,346,169,349,175,357,176,365,170,376,162,389,162,398,165,410,171,417,180,420,165,423,148,432,134,439,122,446,122,389,96" shape="poly"/>
    <area alt="Bourgogne-Franche-Comté" title="Bourgogne-Franche-Comté" href="previsions.php?region=Bourgogne-Franche-Comté" coords="313,181,282,172,282,184,278,195,281,211,282,223,284,231,294,229,302,234,309,240,314,247,317,250,326,249,338,241,351,238,358,242,362,246,369,250,378,232,386,226,415,200,381,174,371,190,361,203,280,163,287,181,327,175,341,198,362,194,286,181" shape="poly"/>
    <area alt="Auvergne-Rhône-Alpes" title="Auvergne-Rhône-Alpes" href="previsions.php?region=Auvergne-Rhône-Alpes" coords="309,272,291,241,283,243,274,242,268,246,265,253,265,262,266,275,265,284,265,297,261,309,253,314,249,326,267,312,276,312,286,312,295,317,303,321,314,335,319,343,331,341,343,342,350,342,358,330,372,323,374,310,405,306,414,295,402,286,406,256,344,260" shape="poly"/>
    <area alt="Provence-Alpes-Côte d'Azur" title="Provence-Alpes-Côte d'Azur" href="previsions.php?region=Provence-Alpes-Côte%20d'Azur" coords="394,321,387,328,379,333,374,338,372,345,371,354,369,362,362,367,349,367,339,363,339,375,327,386,335,387,321,393,337,401,349,402,361,405,377,411,397,410,412,391,453,378,402,323" shape="poly"/>
    <area alt="Occitanie" title="Occitanie" href="previsions.php?region=Occitanie" coords="221,331,213,346,209,355,205,362,195,366,189,370,181,375,175,377,169,377,176,386,176,399,173,407,169,411,165,418,171,430,184,428,191,428,199,426,201,431,211,438,225,438,236,443,254,454,275,440,297,392,257,417,320,377,314,362,255,342,320,371,297,355,296,334,289,353,263,333,257,342,305,350,283,331,290,351,309,357,231,333,281,350" shape="poly"/>
    <area alt="Nouvelle-Aquitaine" title="Nouvelle-Aquitaine" href="previsions.php?region=Nouvelle-Aquitaine" coords="154,432,139,417,130,417,99,408,124,385,125,372,128,355,131,344,161,359,204,279,136,260,168,358,174,356,182,355,189,351,197,342,204,326,213,311,221,308,233,310,241,304,247,291,246,270,169,230,110,312,187,234,149,296,181,237,92,328,166,360,151,380,181,231,155,225,151,303,168,225,157,366,198,239,153,396,161,359,165,329,132,264,161,350" shape="poly"/>
    <area alt="Centre-Val de Loire" title="Centre-Val de Loire" href="previsions.php?region=Centre-Val%20de%20Loire" coords="243,240,237,243,227,244,216,244,208,237,205,224,201,218,189,213,182,210,182,198,189,192,197,186,202,178,205,170,205,154,210,142,215,131,221,138,232,152,235,162,245,161,254,169,269,169,264,190,263,206,265,225,256,229,249,236,265,224,270,216,265,225" shape="poly"/>
    <area alt="Île-de-France" title="Île-de-France" href="previsions.php?region=Île-de-France" coords="229,111,237,111,242,113,251,115,260,117,273,119,278,123,281,129,281,135,279,143,278,146,263,158,251,148,243,146,238,139,231,131,229,123" shape="poly"/>
    <area alt="Normandie" title="Normandie" href="previsions.php?region=Normandie" coords="213,60,225,76,224,91,225,100,220,108,216,113,214,120,206,121,199,128,194,136,193,152,181,140,174,144,168,136,158,139,147,139,139,138,125,140,114,128,113,113,97,102,106,93,104,78,126,80" shape="poly"/>
    <area alt="Bretagne" title="Bretagne" href="previsions.php?region=Bretagne" coords="128,149,128,160,125,169,117,171,104,174,97,179,90,187,79,186,70,186,60,183,48,178,32,177,21,177,20,170,6,168,5,142,57,120,85,134,93,139" shape="poly"/>
    <area alt="Pays de la Loire" title="Pays de la Loire" href="previsions.php?region=Pays%20de%20la%20Loire" coords="147,170,141,153,157,150,169,153,177,154,184,162,193,166,186,178,181,184,169,191,171,195,165,210,149,214,141,214,136,225,145,246,132,247,122,246,120,254,108,247,93,239,89,202,117,206,87,203,102,195" shape="poly"/>
    <area alt="Corse" title="Corse" href="previsions.php?region=Corse" coords="493,402,488,422,483,426,476,432,469,436,461,444,468,457,469,463,470,470,473,475,475,482,485,484,497,470,500,458,497,445,496,432" shape="poly"/>
</map>
<!-- 🏛 Affichage de la région sélectionnée -->
<h2>Sélection du département et de la ville</h2>

<?php if ($selectedRegion) : ?>
    <p>Vous avez sélectionné la région : <strong><?= htmlspecialchars($selectedRegion) ?></strong></p>
<?php else : ?>
    <p>Aucune région sélectionnée.</p>
<?php endif; ?>

<!-- 🏛 Sélection du département -->
<?php if ($selectedRegion) : ?>
    <?php
    /**
     * Recherche du code de la région sélectionnée.
     *
     * Le tableau $regions est parcouru pour trouver le code de la région (REG)
     * correspondant au nom de région (NCCENR) sélectionné par l'utilisateur.
     *
     * Cela permet ensuite de filtrer les départements appartenant à cette région.
     *
     * @var string|null $regionCode Code de la région trouvée (ex : '84' pour Auvergne-Rhône-Alpes)
     */
    $regionCode = null;
    foreach ($regions as $region) {
        if ($region['NCCENR'] === $selectedRegion) {
            $regionCode = $region['REG'];
            break;
        }
    }
    ?>

    <form method="GET">
        <label for="departement">Choisissez un département :</label>
        <select name="departement" id="departement" onchange="this.form.submit()">
            <option value="">-- Sélectionner --</option>
            <?php foreach ($departements as $dept) : ?>
                <?php if ($dept['REG'] == $regionCode) : ?>
                    <option value="<?= htmlspecialchars($dept['NCCENR']) ?>" 
                            <?= ($selectedDepartement === $dept['NCCENR']) ? 'selected="selected"' : '' ?>>
                        <?= htmlspecialchars($dept['LIBELLE']) ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="region" value="<?= htmlspecialchars($selectedRegion) ?>"/>
    </form>
<?php endif; ?>

<!-- 🌤 Affichage de la météo -->
<?php if ($selectedDepartement) : ?>
    <?php
     /**
     * Recherche du code du département sélectionné.
     *
     * Après soumission du formulaire, on récupère le code du département
     * (champ DEP dans les données) en comparant son nom (NCCENR)
     * avec le département sélectionné par l'utilisateur.
     *
     * Ce code est ensuite utilisable pour charger la liste des villes, ou des données météo.
     *
     * @var string|null $departementCode Code du département trouvé (ex : '75' pour Paris)
     */
    $departementCode = null;
    foreach ($departements as $dept) {
        if ($dept['NCCENR'] === $selectedDepartement) {
            $departementCode = $dept['DEP'];//DEP : le code du departement
            break;
        }
    }
    ?>
<?php endif; ?>

<?php
/**
 *  Variables utilisées :
 * - $selectedVille : Code INSEE de la ville sélectionnée (via $_GET)
 * - $villes : Liste de toutes les villes disponibles (chargée depuis CSV)
 * - $villeNom : Nom de la ville correspondant au code INSEE
 */
$villeNom = '';
if ($selectedVille) {
    foreach ($villes as $vil) {
        if ($vil['INSEE'] == $selectedVille) {
            $villeNom = $vil['NOM'];
            break;
        }
    }
}
?>
<form method="GET">
    <label for="ville">Choisissez une ville :</label>
    <select name="ville" id="ville" onchange="this.form.submit()">
        <option value="">-- Sélectionner --</option>
        <?php if ($departementCode) : ?>
            
            <?php usort($villes, function ($a, $b) {
    return strcmp($a['NOM'], $b['NOM']);
});
?>
<?php
$selectionFaite = false; // Variable pour suivre si une sélection a déjà été faite
?>

<?php foreach ($villes as $vil) : ?>
    <?php if ($vil['DEP'] == $departementCode) : ?>
        <option value="<?= htmlspecialchars($vil['INSEE']) ?>" 
                <?php 
                if (!$selectionFaite && $selectedVille === $vil['INSEE']) {
                    echo 'selected="selected"';
                    $selectionFaite = true; // Marquer qu'une sélection a été faite
                }
                ?>>
            <?= htmlspecialchars($vil['NOM']) ?>
        </option>
    <?php endif; ?>
<?php endforeach; ?>
        <?php else : ?>
            <option value="">Aucune ville trouvée</option>
        <?php endif; ?>
    </select>
    <input type="hidden" name="region" value="<?= htmlspecialchars($selectedRegion) ?>"/>
    <input type="hidden" name="departement" value="<?= htmlspecialchars($selectedDepartement) ?>"/>
</form>
<?php
/**
 * 🌤 Affichage des prévisions météo quotidiennes pour une ville sélectionnée.
 *
 * Ce script utilise une fonction externe getWeatherForecast() (non incluse ici)
 * pour récupérer les données météo depuis une API externe (type WeatherAPI, OpenWeatherMap, etc.).
 * Il affiche ensuite les prévisions jour par jour : température minimale, maximale,
 * description météo et icône correspondante.
 *
 * @param string $villeNom Nom de la ville pour laquelle la météo doit être affichée.
 * @return void
 */
$forecastData = getWeatherForecast($villeNom);
//Vérification que les données sont bien récupérées et contiennent des prévisions
if ($forecastData && isset($forecastData['forecast']['forecastday'])) {
    echo "<div class='meteo-container'>";
      /**
     * Boucle à travers les prévisions journalières
     *
     * Pour chaque jour, on extrait :
     * - la date
     * - les températures minimale et maximale (en °C)
     * - la description du temps (ex: "Ensoleillé", "Pluie modérée")
     * - une icône météo fournie par l’API
     */
    
    foreach ($forecastData['forecast']['forecastday'] as $day) {
        $date = date("Y-m-d", strtotime($day['date']));
        $tempMin = isset($day['day']['mintemp_c']) ? $day['day']['mintemp_c'] : "N/A";
        $tempMax = isset($day['day']['maxtemp_c']) ? $day['day']['maxtemp_c'] : "N/A";
        $weatherDescription = isset($day['day']['condition']['text']) ? $day['day']['condition']['text'] : 'Non disponible';
        $iconUrl = isset($day['day']['condition']['icon']) ? $day['day']['condition']['icon'] : '';
        
        echo "<div class='meteo-card'>";
        echo "<div class='meteo-date'>$date</div>";
        echo "<div class='meteo-icon'>" . ($iconUrl ? "<img src='https:$iconUrl' alt='$weatherDescription'/>" : '') . "</div>";
        echo "<div class='meteo-desc'>$weatherDescription</div>";
        echo "<div class='meteo-temp'>Min: {$tempMin}°C</div>";
        echo "<div class='meteo-temp'>Max: {$tempMax}°C</div>";
        echo "</div>";
    }
    
    echo "</div>";
} else {
    echo "<p>Aucune ville sélectionnée</p>";
}
?>

<?php include("./include/footer.inc.php"); ?>