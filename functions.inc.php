<?php
// Fonction pour récupérer les données de l'API APOD de la NASA
function getProcessedJSON() {
    $url = "https://api.nasa.gov/planetary/apod?api_key=62E9zCCG0IuJ3u0RVts05k9x5T6wBh8aLKhNiBzu";
    $data = file_get_contents($url);
    return json_decode($data, true);
}

// Fonction pour récupérer l'adresse IP du visiteur
function getVisitorIP() {
    return $_SERVER['REMOTE_ADDR'];
}

// Fonction pour récupérer les données de géolocalisation en JSON
function getGeoLocationJSON($ip) {
    $url = "https://ipinfo.io/$ip/geo";
    $data = file_get_contents($url);
    return json_decode($data, true);
}

// Fonction pour récupérer les données de géolocalisation en XML
function getGeoLocationXML($ip) {
    $url = "http://www.geoplugin.net/xml.gp?ip=$ip";
    $data = file_get_contents($url);
    return simplexml_load_string($data);
}


// Fonction pour obtenir la date du jour
function getCurrentDate() {
    return date("d/m/Y");
}

function updateHits() {
    $file = 'hits.txt';  // Nom du fichier pour enregistrer les hits
    if (file_exists($file)) {
        $currentHits = file_get_contents($file);  // Lire le nombre actuel de visites
        $currentHits = (int) $currentHits;  // Convertir en entier
    } else {
        $currentHits = 0;  // Si le fichier n'existe pas, initialiser à 0
    }

    // Incrémenter le compteur de visites
    $currentHits++;

    // Sauvegarder la nouvelle valeur dans le fichier
    file_put_contents($file, $currentHits);
    return $currentHits;
}

function readCSV($filename) {
    $data = [];
     // Ouvrir le fichier en mode lecture
    if (($handle = fopen($filename, "r")) !== FALSE) {
        // Lire la première ligne pour récupérer les noms de colonnes (en-têtes)
        $headers = fgetcsv($handle, 1000, ","); // Change ici selon ton fichier : "," ou "\t"
            // Si la lecture des en-têtes échoue, fermer le fichier et retourner un tableau vide
        if ($headers === FALSE) {
            fclose($handle);
            return [];
        }
  // Lire chaque ligne du fichier et créer un tableau associatif
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $data[] = array_combine($headers, $row);
        }
        fclose($handle);
    }
    return $data;
}
function readCSV_villes($filename) {
    $rows = [];
    // Ouvrir le fichier en mode lecture
    if (($handle = fopen($filename, "r")) !== FALSE) {
         // Lire chaque ligne du fichier CSV
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $rows[] = [
                'INSEE' => $data[0],  // Code INSEE : nom officiel dune region ou d un departement
                'NOM' => $data[1],    // Nom de la ville
                'CP' => $data[2],     // Code Postal
                'DEP' => $data[7],    // Code du département
                'REGION' => $data[9]  // Nom de la région
            ];
        }
        fclose($handle);
    }
    return $rows;
}

function getWeatherForecast($villeNom) {
    // Vérification si le nom de la ville est valide
    if (empty($villeNom)) {
        return false; // Retourne faux si la ville est vide
    }

    // Clé API (à ne pas partager publiquement)
    $apiKey = '9d6d1ba4c5a2406dab3110950251504';

    // URL de l'API
    $url = "https://api.weatherapi.com/v1/forecast.json?key=$apiKey&q=" . urlencode($villeNom) . "&days=7&aqi=no&alerts=no&lang=fr";

    // Appel à l'API et gestion des erreurs
    $response = file_get_contents($url);

    // Vérifie si l'appel a échoué
    if ($response === FALSE) {
        return false; // Retourne faux en cas d'erreur
    }

    // Décodage de la réponse JSON
    $forecastData = json_decode($response, true);
    

    // Si l'API retourne une erreur, on l'affiche
    if (isset($forecastData['error'])) {
        return false; // Retourne faux en cas d'erreur de l'API
    }

    return $forecastData; // Retourne les données des prévisions si tout est correct
}
function getRandomImage($directory = "./images/photos/") {
    if (!is_dir($directory)) {
        return null;
    }

    $images = glob($directory . "*.{jpg,jpeg,png,webp}", GLOB_BRACE);

    if (empty($images)) {
        return null;
    }

    $randomImage = $images[array_rand($images)];
    return $randomImage;
}

function getImageCaption($imagePath) {
    $captions = include("./images/photos/captions.php");
    $imageName = basename($imagePath);
    
    return $captions[$imageName] ?? "Image météo";
}
// Fonction pour enregistrer une consultation de ville dans le fichier CSV
function logCityVisit($villeCode) {
    $file = 'villes_consultees.csv'; // Fichier de stockage
    $date = date('Y-m-d H:i:s');

    if (($handle = fopen($file, 'a')) !== false) {
        fputcsv($handle, array($villeCode, $date));
        fclose($handle);
    } else {
        echo "Erreur d'ouverture du fichier.";
    }
}

// Fonction pour définir un cookie avec la dernière ville consultée
function setCityCookie($villeCode) {
    $expire = time() + (3600 * 24 * 30); // 30 jours
    setcookie('derniere_ville', $villeCode, $expire, '/');
}

// Fonction pour récupérer la dernière ville consultée depuis le cookie
function getLastVisitedCity() {
    return $_COOKIE['derniere_ville'] ?? null;
}

// Fonction pour charger les noms des villes depuis v_ville_2024.csv
function loadCityNames() {
    $cityNames = [];
    $file = 'v_ville_2024.csv';

    if (($handle = fopen($file, 'r')) !== false) {
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $insee = $data[0];
            $name = $data[1];
            $cityNames[$insee] = $name;
        }
        fclose($handle);
    }

    return $cityNames;
}

// Fonction pour récupérer les statistiques de visites par ville (par code INSEE)
function getCityVisitStats() {
    $file = 'villes_consultees.csv';
    $stats = [];

    if (($handle = fopen($file, 'r')) !== false) {
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $code = $data[0];

            if (isset($stats[$code])) {
                $stats[$code]++;
            } else {
                $stats[$code] = 1;
            }
        }
        fclose($handle);
    }

    return $stats;
}

// Fonction pour afficher les statistiques avec noms des villes
function displayCityStats() {
    $visitStats = getCityVisitStats();
    $cityNames = loadCityNames();
    arsort($visitStats);
    $total = array_sum($visitStats);
    
    // Limiter à 15 villes
    $visitStats = array_slice($visitStats, 0, 10, true);
    
    echo "<p><strong>Total des consultations :</strong> $total</p>";
    echo "<p>Voici les villes les plus consultées sur notre site :</p>";
    echo "<ul>";
    foreach ($visitStats as $code => $count) {
        $name = $cityNames[$code] ?? "Code INSEE $code inconnu";
        echo "<li><strong>$name</strong> : $count consultations</li>";
    }
    echo "</ul>";
}
function generateCitySVG() {
    $visitStats = getCityVisitStats();
    $cityNames = loadCityNames();
    arsort($visitStats);
    
    // Garde les 10 villes les plus consultées
    $visitStats = array_slice($visitStats, 0, 10, true);
    
    // Paramètres du SVG
    $barWidth = 40;
    $spacing = 50;
    $margin = 80;
    $barCount = count($visitStats);
    $width = max(800, $margin * 2 + $barCount * ($barWidth + $spacing));
    $height = 400;
    
    // Calcul max
    $maxCount = max($visitStats);
    
    // Fonction pour tronquer les noms trop longs
    function truncate($string, $maxLength = 12) {
        return (strlen($string) > $maxLength) ? substr($string, 0, $maxLength - 3) . '...' : $string;
    }
    
    // Commencer le code SVG
    $svg = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';
    $svg .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$width.' '.$height.'">';
    $svg .= '<rect width="100%" height="100%" fill="white"/>';
    
    // Dessiner les axes
    $svg .= '<line x1="'.$margin.'" y1="'.$margin.'" x2="'.$margin.'" y2="'.($height - $margin).'" stroke="black" stroke-width="1"/>';
    $svg .= '<line x1="'.$margin.'" y1="'.($height - $margin).'" x2="'.($width - $margin).'" y2="'.($height - $margin).'" stroke="black" stroke-width="1"/>';
    
    // Dessiner les barres
    $x = $margin;
    foreach ($visitStats as $code => $count) {
        $barHeight = (int)(($height - 2 * $margin) * ($count / $maxCount));
        $y1 = $height - $margin;
        $y2 = $y1 - $barHeight;
        
        // Barre
        $svg .= '<rect x="'.$x.'" y="'.$y2.'" width="'.$barWidth.'" height="'.$barHeight.'" fill="#6464FF"/>';
        
        // Valeur au-dessus
        $svg .= '<text x="'.($x + 5).'" y="'.($y2 - 5).'" font-size="12" fill="black">'.$count.'</text>';
        
        // Nom de la ville en bas
        $name = truncate($cityNames[$code] ?? $code);
        $svg .= '<text x="'.($x + $barWidth/2).'" y="'.($height - $margin + 15).'" font-size="12" text-anchor="middle" fill="black">'.$name.'</text>';
        
        $x += $barWidth + $spacing;
    }
    
    // Finir le SVG
    $svg .= '</svg>';
    
    return $svg;
}

// Fonction pour écrire le SVG dans un fichier
function saveCitySVG($filename = 'histogram.svg') {
    $svg = generateCitySVG();
    file_put_contents($filename, $svg);
}

// Fonction pour afficher directement le SVG dans la page
function displayCitySVG() {
    echo generateCitySVG();
}
// Fonction pour récupérer les fichiers PHP du répertoire
function getPHPFiles($directory) {
    $files = [];
    $excludeFiles = ['plan.php', 'captions.php'];
    $excludeDirs = ['td', 'include', 'CSS', 'images'];
   
    if ($handle = opendir($directory)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                // Si c'est un fichier PHP qui n'est pas dans la liste d'exclusion
                if (is_file($directory . '/' . $entry) && pathinfo($entry, PATHINFO_EXTENSION) === 'php' && !in_array($entry, $excludeFiles)) {
                    $files[] = $entry;
                }
                // Si c'est un répertoire qui n'est pas exclu
                elseif (is_dir($directory . '/' . $entry) && !in_array($entry, $excludeDirs)) {
                    $subFiles = getPHPFiles($directory . '/' . $entry);
                    foreach ($subFiles as $subFile) {
                        $files[] = $entry . '/' . $subFile;
                    }
                }
            }
        }
        closedir($handle);
    }
   
    return $files;
}

function getPageTitle($file) {
    // Vérifier si le fichier existe
    if (!file_exists($file)) {
        return ucfirst(pathinfo($file, PATHINFO_FILENAME));
    }
    
    $content = file_get_contents($file);
   
    // Expression régulière pour capturer la valeur de $pageTitle
    if (preg_match('/\$pageTitle\s*=\s*(["\'])(.*?)\1\s*;/', $content, $matches)) {
        return trim($matches[2]);
    } else {
        // Si le titre n'est pas trouvé, utiliser le nom du fichier
        $title = pathinfo($file, PATHINFO_FILENAME);
        return ucfirst(str_replace('-', ' ', $title));
    }
}
/**
 * Récupère les informations de région et département d'une ville à partir de son code INSEE
 * 
 * @param string $cityCode Le code INSEE de la ville
 * @return array|null Les informations de la ville ou null si non trouvé
 */
function getCityInfoByCode($cityCode) {
    $villes = readCSV_villes("v_ville_2024.csv");
    $departements = readCSV("v_departement_2024.csv");
    $regions = readCSV("v_region_2024.csv");
    
    // Chercher la ville
    $villeInfo = null;
    foreach ($villes as $ville) {
        if ($ville['INSEE'] == $cityCode) {
            $villeInfo = $ville;
            break;
        }
    }
    
    if (!$villeInfo) {
        return null;
    }
    
    // Chercher le département
    $deptCode = $villeInfo['DEP'];
    $deptInfo = null;
    foreach ($departements as $dept) {
        if ($dept['DEP'] == $deptCode) {
            $deptInfo = $dept;
            break;
        }
    }
    
    if (!$deptInfo) {
        return null;
    }
    
    // Chercher la région
    $regionCode = $deptInfo['REG'];
    $regionInfo = null;
    foreach ($regions as $region) {
        if ($region['REG'] == $regionCode) {
            $regionInfo = $region;
            break;
        }
    }
    
    if (!$regionInfo) {
        return null;
    }
    
    // Retourner toutes les informations nécessaires
    return [
        'region' => $regionInfo['NCCENR'],
        'departement' => $deptInfo['NCCENR'],
        'ville' => $villeInfo['INSEE'],
        'nom_ville' => $villeInfo['NOM']
    ];
}


function getActivityRecommendations($weatherData) {
    // Extraire les données météo pertinentes
    $temp = $weatherData['current']['temp_c'];
    $humidity = $weatherData['current']['humidity'];
    $wind = $weatherData['current']['wind_kph'];
    $precip = $weatherData['current']['precip_mm'];
    $condition = $weatherData['current']['condition']['text'];
    
    // Calculer les scores d'activités
    $hikingScore = calculateHikingScore($temp, $humidity, $wind, $precip, $condition);
    $beachScore = calculateBeachScore($temp, $humidity, $wind, $precip, $condition);
    $bikingScore = calculateBikingScore($temp, $humidity, $wind, $precip, $condition);
    
    return [
        'hiking' => $hikingScore,
        'beach' => $beachScore,
        'biking' => $bikingScore
    ];
}

function calculateHikingScore($temp, $humidity, $wind, $precip, $condition) {
    $score = 100;
    
    // Température idéale entre 15 et 25°C
    if ($temp < 10 || $temp > 30) $score -= 30;
    else if ($temp < 15 || $temp > 25) $score -= 15;
    
    // Pénalité pour pluie
    if ($precip > 5) $score -= 50;
    else if ($precip > 0) $score -= 20;
    
    // Pénalité pour vent fort
    if ($wind > 30) $score -= 30;
    else if ($wind > 20) $score -= 15;
    
    // Conditions défavorables
    if (stripos($condition, 'pluie') !== false || 
        stripos($condition, 'neige') !== false || 
        stripos($condition, 'orage') !== false) {
        $score -= 40;
    }
    
    return max(0, min(100, $score));
}

function calculateBeachScore($temp, $humidity, $wind, $precip, $condition) {
    $score = 100;
    
    // Température idéale supérieure à 25°C
    if ($temp < 20) $score -= 50;
    else if ($temp < 25) $score -= 25;
    
    // Pénalité pour pluie
    if ($precip > 0) $score -= 40;
    
    // Pénalité pour vent fort
    if ($wind > 25) $score -= 30;
    
    // Conditions défavorables
    if (stripos($condition, 'nuageux') !== false) $score -= 20;
    if (stripos($condition, 'pluie') !== false) $score -= 50;
    if (stripos($condition, 'orage') !== false) $score -= 70;
    
    return max(0, min(100, $score));
}

function calculateBikingScore($temp, $humidity, $wind, $precip, $condition) {
    $score = 100;
    
    // Température idéale entre 15 et 28°C
    if ($temp < 10 || $temp > 32) $score -= 30;
    else if ($temp < 15 || $temp > 28) $score -= 15;
    
    // Pénalité pour pluie
    if ($precip > 2) $score -= 60;
    else if ($precip > 0) $score -= 30;
    
    // Pénalité pour vent très fort
    if ($wind > 35) $score -= 50;
    else if ($wind > 25) $score -= 20;
    
    // Conditions défavorables
    if (stripos($condition, 'pluie') !== false || 
        stripos($condition, 'orage') !== false) {
        $score -= 50;
    }
    
    return max(0, min(100, $score));
}

?>