<?php
require_once("./include/functions.inc.php");
ob_start();
$pageTitle = "Ma Météo - Prévisions Météo & Climat";
$pageDescription = "Choisissez un département après avoir sélectionné votre région puis une ville pour voir la météo détaillée.";

include("./include/header.inc.php");

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

    // Tu peux ensuite afficher les prévisions météo ici
} else {
    echo "<p>Aucune ville sélectionnée.</p>";
}
// Vérifier si l'utilisateur arrive sur la page d'accueil sans paramètres
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
</div>
<h2>🗺 Cliquez sur une région de la carte</h2>
<div class="map-container">
<figure>
<img src="images/carteFrance.png" usemap="#image-map" alt="Carte des régions de France">
<figcaption>🗺 Cliquez sur une région pour commencer</figcaption>
</figure>
</div>
<map name="image-map">
    <area target="" alt="Grand Est" title="Grand Est" href="previsions.php?region=Grand%20Est" coords="660,128,648,152,636,182,618,190,619,204,613,235,609,262,621,278,639,279,641,303,662,302,679,302,705,312,713,322,714,317,711,332,722,330,740,314,694,309,755,302,770,301,792,306,811,317,830,324,834,346,848,332,849,306,850,284,860,266,866,245,881,225,741,194" shape="poly">
    <area target="" alt="Île-de-France" title="Île-de-France" href="previsions.php?region=Île-de-France" coords="487,207,487,217,486,234,491,242,498,250,512,260,514,272,525,271,535,275,548,282,557,282,564,273,577,262,587,258,591,239,583,221,554,214,523,207,504,201" shape="poly">
    <area target="" alt="Hauts-de-France" title="Hauts-de-France" href="previsions.php?region=Hauts-de-France" coords="593,189,597,206,581,199,564,195,547,189,530,187,511,181,499,188,494,166,495,149,493,134,485,115,475,104,490,103,490,86,490,73,491,51,493,38,493,24,510,25,530,18,547,35,561,54,577,50,591,66,602,77,610,87,629,89,637,108,636,136,633,157,606,184,629,166,637,123,643,123,621,162,629,153,613,169,609,174,604,190,599,220" shape="poly">
    <area target="" alt="Normandie" title="Normandie" href="previsions.php?region=Normandie" coords="269,152,283,152,275,166,277,177,286,191,289,210,289,226,291,244,289,254,310,248,329,248,337,252,357,252,374,249,381,258,399,256,413,264,425,276,428,262,410,274,425,252,431,234,442,226,455,222,465,222,465,213,468,197,478,186,477,191,484,168,480,151,477,136,481,129,472,122,469,113" shape="poly">
    <area target="" alt="Bretagne" title="Bretagne" href="previsions.php?region=Bretagne" coords="275,258,303,281,298,304,298,319,286,319,268,325,253,329,241,339,233,345,216,341,197,336,189,333,178,324,166,314,159,314,142,311,129,308,109,307,105,315,106,299,112,289,124,275,115,263,102,259,85,263,105,246,149,244,166,237,179,238,304,274" shape="poly">
    <area target="" alt="Pays de la Loire" title="Pays de la Loire" href="previsions.php?region=Pays%20de%20la%20Loire" coords="304,407,333,440,308,450,294,453,278,445,261,431,256,418,254,403,254,390,254,377,251,361,241,363,233,365,262,346,278,338,313,340,329,300,321,282,334,271,357,267,366,271,379,275,395,271,399,281,291,431,318,373,356,289,386,293,409,291,336,376,346,374,372,382,317,401,372,332,401,301,421,302,410,321,417,324,373,362,381,367" shape="poly">
    <area target="" alt="Nouvelle-Aquitaine" title="Nouvelle-Aquitaine" href="previsions.php?region=Nouvelle-Aquitaine" coords="366,448,533,482,526,499,516,524,523,549,509,570,493,575,472,576,463,592,449,610,438,634,429,651,404,662,388,661,350,664,347,694,302,690,330,650,353,752,348,465,349,710,367,731,361,748,357,760,343,759,330,746,317,737,302,735,293,717,297,682,306,659,316,635,337,606,323,586,313,587,314,560,354,530,335,521,328,497,327,472,321,512,322,526,341,488,330,480,343,474,351,446,349,408,417,418,353,710,349,710,394,429,381,473,357,678" shape="poly">
    <area target="" alt="Occitanie" title="Occitanie" href="previsions.php?region=Occitanie" coords="406,775,391,775,374,775,374,759,386,741,382,728,378,706,371,690,393,685,414,681,436,670,444,655,452,644,458,626,466,617,481,602,475,594,492,599,499,597,505,609,510,624,524,624,545,616,552,608,559,621,566,621,583,610,606,613,614,619,618,629,622,645,637,656,654,658,669,662,655,690,630,703,609,714,581,734,563,748,542,752,445,768,501,802,569,798,571,746" shape="poly">
    <area target="" alt="Auvergne-Rhône-Alpes" title="Auvergne-Rhône-Alpes" href="previsions.php?region=Auvergne-Rhône-Alpes" coords="622,481,596,446,578,439,561,435,546,444,540,459,545,471,550,495,544,516,543,534,541,560,529,570,523,581,522,604,545,585,565,580,594,572,605,584,623,593,633,611,646,621,660,631,672,633,695,634,713,636,729,618,734,599,751,581,773,565,795,551,683,482,703,493,709,471,736,479,752,490,772,492,793,481,803,469,801,530,788,543,817,546,692,492,786,536,673,482" shape="poly">
    <area target="" alt="Bourgogne-Franche-Comté" title="Bourgogne-Franche-Comté" href="previsions.php?region=Bourgogne-Franche-Comté" coords="587,316,586,306,580,327,576,350,580,378,583,408,595,418,618,421,628,439,646,453,663,460,680,454,689,442,704,439,720,445,739,450,764,428,746,450,771,429,778,399,594,306,782,404,734,347,792,375,767,383,799,385,815,354,768,326,762,369,779,367,779,370,771,370,617,324" shape="poly">
    <area target="" alt="Centre-Val de Loire" title="Centre-Val de Loire" href="previsions.php?region=Centre-Val%20de%20Loire" coords="468,247,452,262,446,278,447,299,442,319,436,342,422,355,407,372,402,386,413,396,430,393,446,415,444,426,458,438,489,446,503,446,520,445,531,430,545,418,554,402,554,374,552,338,515,303,543,328,543,333" shape="poly">
    <area target="" alt="Provence-Alpes-Côte d'Azur" title="Provence-Alpes-Côte d'Azur" href="previsions.php?region=Provence-Alpes-Côte%20d'Azur" coords="792,588,789,613,786,644,773,610,758,610,748,624,743,638,760,640,775,641,772,655,752,661,728,667,700,662,689,679,679,705,699,708,722,713,748,724,765,710,769,728,784,719,803,706,821,688,843,672,856,666,829,700,809,710,798,724,782,730,759,734,734,731,709,719,800,725,817,686,823,674" shape="poly">
    <area target="" alt="Corse" title="Corse" href="previsions.php?region=Corse" coords="964,794,953,798,941,810,940,816,940,825,940,836,940,851,945,864,951,874,955,888,966,865,970,851,973,835,973,821,975,802,975,785,938,800,932,813,977,772" shape="poly">
</map>


<h2>Sélection du département et de la ville</h2>

<?php if ($selectedRegion) : ?>
    <p>Vous avez sélectionné la région : <strong><?= htmlspecialchars($selectedRegion) ?></strong></p>
<?php else : ?>
    <p>Aucune région sélectionnée.</p>
<?php endif; ?>

<!-- 🏛 Sélection du département -->
<?php if ($selectedRegion) : ?>
    <?php
    // Trouver le code de la région sélectionnée
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
                            <?= ($selectedDepartement === $dept['NCCENR']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dept['LIBELLE']) ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="region" value="<?= htmlspecialchars($selectedRegion) ?>">
    </form>
<?php endif; ?>

<!-- 🌤 Affichage de la météo -->
<?php if ($selectedDepartement) : ?>
    <?php
    // Trouver le code du département sélectionné
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
// Récupérer le nom de la ville en fonction du code INSEE
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

            
            <?php foreach ($villes as $vil) : ?>
                <?php if ($vil['DEP'] == $departementCode) : ?>
                    <option value="<?= htmlspecialchars($vil['INSEE']) ?>" 
                            <?= ($selectedVille === $vil['INSEE']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($vil['NOM']) ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else : ?>
            <option value="">Aucune ville trouvée</option>
        <?php endif; ?>
    </select>
    <input type="hidden" name="region" value="<?= htmlspecialchars($selectedRegion) ?>">
    <input type="hidden" name="departement" value="<?= htmlspecialchars($selectedDepartement) ?>">
</form>
<?php
// Supposons que vous avez déjà la fonction qui récupère les données de l'API
$forecastData = getWeatherForecast($villeNom);
//ont définies dans les données retournées par l'API météo 
// Section prévisions météo
if ($forecastData && isset($forecastData['forecast']['forecastday'])) {
    echo "<div class='meteo-container'>";
    
    // Parcours des prévisions pour chaque jour
    foreach ($forecastData['forecast']['forecastday'] as $day) {
        $date = date("Y-m-d", strtotime($day['date']));
        $tempMin = isset($day['day']['mintemp_c']) ? $day['day']['mintemp_c'] : "N/A";
        $tempMax = isset($day['day']['maxtemp_c']) ? $day['day']['maxtemp_c'] : "N/A";
        $weatherDescription = isset($day['day']['condition']['text']) ? $day['day']['condition']['text'] : 'Non disponible';
        $iconUrl = isset($day['day']['condition']['icon']) ? $day['day']['condition']['icon'] : '';
        
        echo "<div class='meteo-card'>";
        echo "<div class='meteo-date'>$date</div>";
        echo "<div class='meteo-icon'>" . ($iconUrl ? "<img src='https:$iconUrl' alt='$weatherDescription'>" : '') . "</div>";
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