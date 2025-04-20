<?php
/**
 * @file functions.inc.php
 *
 * @author Ouardia/Lisa
 * @brief Fichier contenant un ensemble de fonctions utilitaires pour un site météo interactif.
 *
 * Fonctions incluses :
 * - Extraction dynamique du titre d'une page PHP.
 * - Récupération des informations géographiques (ville, département, région) à partir d'un code INSEE.
 * - Calcul de recommandations d’activités en fonction de la météo (randonnée, plage, vélo).
 * - Gestion des cookies d’acceptation liés au consentement utilisateur.
 * - Récupération de l'image du jour via l'API APOD de la NASA.
 * 
 * Ce fichier est conçu pour centraliser la logique métier et favoriser la réutilisabilité du code.
 *
 * PHP version 8.1+
 * 
 */

// ==============================
// Fonctions
// ==============================

/**
 * Récupère et décode les données JSON de l'API APOD (Astronomy Picture of the Day) de la NASA.
 *
 * Cette fonction utilise l'URL de l'API APOD avec une clé d'API pour obtenir
 * les informations du jour (image, titre, description, etc.), puis retourne les
 * données sous forme de tableau associatif.
 *
 * @return array|null Les données décodées de l'API sous forme de tableau associatif,
 *                    ou null en cas d'erreur de récupération ou de décodage.
 */
function getProcessedJSON() {
    $url = "https://api.nasa.gov/planetary/apod?api_key=62E9zCCG0IuJ3u0RVts05k9x5T6wBh8aLKhNiBzu";
    $data = file_get_contents($url);
    return json_decode($data, true);
}

/**
 * Récupère l'adresse IP du visiteur.
 *
 * Cette fonction retourne l'adresse IP telle qu'elle est fournie
 * par la variable serveur REMOTE_ADDR.
 *
 * @return string Adresse IP du visiteur.
 */
function getVisitorIP() {
    return $_SERVER['REMOTE_ADDR'];
}

/**
 * Récupère les données de géolocalisation d'une adresse IP au format JSON.
 *
 * Utilise le service ipinfo.io pour obtenir les informations de localisation
 * comme la ville, la région, le pays, etc.
 *
 * @param string $ip L'adresse IP à géolocaliser.
 * @return array|null Les données de géolocalisation sous forme de tableau associatif,
 *                    ou null en cas d'erreur.
 */
function getGeoLocationJSON($ip) {
    $url = "https://ipinfo.io/$ip/geo";
    $data = file_get_contents($url);
    return json_decode($data, true);
}

/**
 * Récupère les données de géolocalisation d'une adresse IP au format XML.
 *
 * Utilise le service geoplugin.net pour obtenir les informations de localisation
 * sous forme d'objet SimpleXMLElement.
 *
 * @param string $ip L'adresse IP à géolocaliser.
 * @return SimpleXMLElement|false Objet XML contenant les informations de géolocalisation,
 *                                ou false en cas d'échec.
 */
function getGeoLocationXML($ip) {
    $url = "http://www.geoplugin.net/xml.gp?ip=$ip";
    $data = file_get_contents($url);
    return simplexml_load_string($data);
}


/**
 * Retourne la date actuelle au format français (jour/mois/année).
 *
 * Utilise la fonction PHP date() pour formater la date.
 *
 * @return string La date du jour au format "d/m/Y".
 */
function getCurrentDate() {
    return date("d/m/Y");
}
/**
 * Met à jour le nombre de visites (hits) dans un fichier texte.
 *
 * Si le fichier hits.txt existe, la fonction lit le nombre actuel de visites,
 * l'incrémente de 1, puis enregistre la nouvelle valeur.
 * Si le fichier n'existe pas, le compteur commence à 1.
 *
 * @return int Le nombre actuel de visites après incrémentation.
 */
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
/**
 * Lit un fichier CSV et retourne les données sous forme de tableau associatif.
 *
 * La première ligne du fichier doit contenir les en-têtes de colonnes.
 *
 * @param string $filename Le chemin vers le fichier CSV.
 * @return array Un tableau contenant les lignes du fichier sous forme de tableaux associatifs.
 */
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
/**
 * Lit un fichier CSV contenant des informations sur des villes
 * et retourne un tableau simplifié avec des colonnes spécifiques.
 *
 * Cette version ne dépend pas de la première ligne comme en-tête.
 *
 * @param string $filename Le chemin vers le fichier CSV.
 * @return array Un tableau contenant les données avec les clés : INSEE, NOM, CP, DEP, REGION.
 */
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
/**
 * Récupère les prévisions météo pour une ville donnée via l'API WeatherAPI.
 *
 * Envoie une requête à l'API pour obtenir les prévisions sur 7 jours.
 * La réponse est renvoyée sous forme de tableau associatif.
 *
 * @param string $villeNom Le nom de la ville pour laquelle obtenir la météo.
 * @return array|false Les données météo sous forme de tableau associatif,
 *                     ou false en cas d'erreur ou si la ville est invalide.
 */
function getWeatherForecast($villeNom) {
    // Vérification si le nom de la ville est valide
    if (empty($villeNom)) {
        return false; // Retourne faux si la ville est vide
    }

    // Clé API (à ne pas partager publiquement)
    $apiKey = 'e614fb08fd0148a0aee122507252004';

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
/**
 * Sélectionne et retourne de façon aléatoire une image depuis un répertoire.
 *
 * Les extensions d’images supportées sont : .jpg, .jpeg, .png, .webp.
 *
 * @param string $directory Le chemin du dossier contenant les images (par défaut : ./images/photos/).
 * @return string|null Le chemin de l'image sélectionnée aléatoirement,
 *                     ou null si le dossier est invalide ou vide.
 */
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
/**
 * Récupère la légende associée à une image.
 *
 * Les légendes sont stockées dans un fichier PHP retournant un tableau associatif.
 * Si aucune légende n'est trouvée pour l'image, une légende par défaut est retournée.
 *
 * @param string $imagePath Le chemin complet de l’image.
 * @return string La légende de l’image ou "Image météo" par défaut.
 */
function getImageCaption($imagePath) {
    $captions = include("./images/photos/captions.php");
    $imageName = basename($imagePath);
    
    return $captions[$imageName] ?? "Image météo";
}
/**
 * Enregistre une consultation de ville avec la date et l'heure actuelle dans un fichier CSV.
 *
 * @param string $villeCode Le code INSEE de la ville consultée.
 * @return void
 */
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

/**
 * Définit un cookie contenant la dernière ville consultée.
 *
 * @param string $villeCode Le code INSEE de la ville.
 * @return void
 */
function setCityCookie($villeCode) {
    $expire = time() + (3600 * 24 * 90); // 90 jours
    setcookie('derniere_ville', $villeCode, $expire, '/');
}

/**
 * Récupère la dernière ville consultée depuis un cookie.
 *
 * @return string|null Le code INSEE de la ville, ou null si non défini.
 */
function getLastVisitedCity() {
    return $_COOKIE['derniere_ville'] ?? null;
}

/**
 * Charge les noms des villes depuis un fichier CSV.
 *
 * @return array Un tableau associatif [code INSEE => nom de la ville].
 */
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

/**
 * Récupère les statistiques de consultation des villes.
 *
 * @return array Un tableau associatif [code INSEE => nombre de consultations].
 */
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

/**
 * Affiche les statistiques de consultation des villes les plus populaires (max 10).
 *
 * Affiche le total des consultations et une liste HTML des villes les plus consultées.
 *
 * @return void
 */
function displayCityStats() {
    $visitStats = getCityVisitStats();
    $cityNames = loadCityNames();
    arsort($visitStats);
    
    // Limiter à 10 villes
    $visitStats = array_slice($visitStats, 0, 10, true);
    
    echo "<div class='city-stats-container'>";
    foreach ($visitStats as $code => $count) {
        $name = $cityNames[$code] ?? "Code INSEE $code inconnu";
        echo "<div class='city-block'>";
        echo "$name : $count consultations";
        echo "</div>";
    }
    echo "</div>";
}

/**
 * Génère une représentation SVG des statistiques de consultation des 10 villes les plus visitées.
 *
 * Crée un graphique à barres avec les noms de villes et le nombre de consultations.
 *
 * @return string Le code SVG généré.
 */
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

/**
 * Enregistre le graphique SVG des villes les plus consultées dans un fichier.
 *
 * @param string $filename Le nom du fichier où enregistrer le SVG. Par défaut : 'histogram.svg'.
 * @return void
 */
function saveCitySVG($filename = 'histogram.svg') {
    $svg = generateCitySVG();
    file_put_contents($filename, $svg);
}

/**
 * Affiche directement le graphique SVG des villes les plus consultées.
 *
 * @return void
 */
function displayCitySVG() {
    echo generateCitySVG();
}
/**
 * Récupère récursivement les fichiers PHP d'un répertoire, en excluant certains fichiers et dossiers.
 *
 * @param string $directory Le chemin du répertoire à parcourir.
 * @return array Liste des fichiers PHP trouvés.
 */
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

/**
 * Tente d'extraire le titre de la page depuis une variable $pageTitle dans le fichier.
 * Si aucun titre n'est trouvé, retourne un nom basé sur le nom du fichier.
 *
 * @param string $file Le chemin vers le fichier PHP.
 * @return string Le titre de la page.
 */
function getPageTitle($file) {
    $content = file_get_contents($file);
    
    // Expression régulière corrigée pour capturer tout le contenu entre guillemets/apostrophes
    if (preg_match('/\$pageTitle\s*=\s*(["\'])(.*?)\1/', $content, $matches)) {
        return trim($matches[2]);
    } else {
        // Fallback si pas trouvé
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
/**
 * Calcule des recommandations d'activités (randonnée, plage, vélo) selon les conditions météo actuelles.
 *
 * @param array $weatherData Données météo au format API, contenant les informations de température, humidité, vent, précipitations et condition.
 * @return array Tableau associatif contenant un score pour chaque activité : 'hiking', 'beach' et 'biking'.
 */
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

/**
 * Calcule un score de recommandation pour la randonnée selon les conditions météo.
 *
 * @param float $temp Température actuelle en degrés Celsius.
 * @param float $humidity Humidité en pourcentage.
 * @param float $wind Vitesse du vent en km/h.
 * @param float $precip Quantité de précipitations en mm.
 * @param string $condition Description textuelle des conditions météo (ex. : "Ensoleillé", "Pluie modérée").
 * @return int Score entre 0 et 100 représentant la praticabilité de la randonnée.
 */

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
/**
 * Calcule un score de recommandation pour une activité à la plage selon les conditions météo.
 *
 * @param float $temp Température actuelle en degrés Celsius.
 * @param float $humidity Humidité en pourcentage.
 * @param float $wind Vitesse du vent en km/h.
 * @param float $precip Quantité de précipitations en mm.
 * @param string $condition Description textuelle des conditions météo (ex. : "Nuageux", "Pluie").
 * @return int Score entre 0 et 100 représentant la praticabilité de la plage.
 */
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
/**
 * Calcule un score de recommandation pour une activité de vélo selon les conditions météo.
 *
 * @param float $temp Température actuelle en degrés Celsius.
 * @param float $humidity Humidité en pourcentage.
 * @param float $wind Vitesse du vent en km/h.
 * @param float $precip Quantité de précipitations en mm.
 * @param string $condition Description textuelle des conditions météo (ex. : "Orage", "Pluie faible").
 * @return int Score entre 0 et 100 représentant la praticabilité du vélo.
 */
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

/**
 * Vérifie si l'utilisateur a déjà accepté les cookies
 * 
 * @return bool True si les cookies sont acceptés, false sinon
 */
function areCookiesAccepted() {
    return isset($_COOKIE['cookies_accepted']) && $_COOKIE['cookies_accepted'] === 'true';
}

/**
 * Définit le cookie d'acceptation avec une durée de 30 jours
 */
function setCookieAcceptance() {
    if (isset($_POST['accept_cookies'])) {
        setcookie('cookies_accepted', 'true', time() + (365  * 24 * 60 * 60), '/');
        // Redirection pour actualiser la page et prendre en compte le cookie
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } 
    
    if (isset($_POST['decline_cookies'])) {
        setcookie('cookies_declined', 'true', time() + (365  * 24 * 60 * 60), '/');
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}



?>