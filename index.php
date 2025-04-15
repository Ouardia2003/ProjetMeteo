<?php
require_once("./include/functions.inc.php");

$pageTitle = "Ma Météo - Page d'accueil";
$pageDescription = "Site de prévisions météorologiques pour la semaine en France.";
$bodyClass = 'home-page';
include("./include/header.inc.php");

$randomImage = getRandomImage("./images/photos/");
$caption = $randomImage ? getImageCaption($randomImage) : "Image météo";
$ip = getVisitorIP();
$geoData = getGeoLocationJSON($ip);
$ville = $geoData['city'] ?? null;
$forecastData = getWeatherForecast($ville);
// Obtenir les recommandations d'activités si les données météo sont disponibles
$activityScores = null;
if ($forecastData && isset($forecastData['current'])) {
    $activityScores = getActivityRecommendations($forecastData);
}
?>

<section class="intro">
    <div class="intro-content">
        <h1>✨ Bienvenue sur Ma Météo ✨</h1>
        <p>
            🌍 <strong>Vous rêvez de connaître la météo de votre ville avec précision</strong> ou de planifier vos prochaines aventures en toute sérénité ?
            Vous êtes au bon endroit ! Choisissez <strong>votre région</strong>, <strong>votre département</strong>, puis votre <strong>ville</strong> pour des prévisions sur-mesure.
        </p>
        <p>
            ☀ Un seul clic, et vous plongez dans une carte interactive pour explorer la météo de <strong>n'importe quelle zone</strong>.
        </p>
        <nav>
        <ul class="navIndex">
            <li><a href="#meteoJour">Consultez la méteo du jour selon votre position</a></li>
            <li><a href="#activites">Voir les activités recommandées</a></li>
            <li><a href="#imageRandom">Découvrez nos images</a></li>
        </ul>
    </nav>
</section>
<section id='meteoJour'>
    <?php
    if ($forecastData && isset($forecastData['current'])) {
        $temp = $forecastData['current']['temp_c'];
        $condition = $forecastData['current']['condition']['text'];
        $icon = $forecastData['current']['condition']['icon'];
        
        echo "<div class='meteo-bloc'>";
        echo "<h2>Météo actuelle à $ville</h2>";
        echo "<div class='infos-meteo'>";
        echo "<p><strong>Température actuelle :</strong> {$temp}°C</p>";
        echo "<p><strong>Description:</strong> $condition</p>";
        echo "<img src='$icon' alt='$condition'>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "<p>Impossible de récupérer la météo pour votre position.</p>";
    }
    ?>
</section>

<section id="activites">
    <?php if ($activityScores): ?>
    <div class="activities-bloc">
        <h2>Activités recommandées</h2>
        <div class="activities-container">
            <div class="activity-card">
                <h3><?php echo $activityScores['hiking']; ?>%</h3>
                <p>Idéal pour randonnée</p>
            </div>
            
            <div class="activity-card">
                <h3><?php echo $activityScores['beach']; ?>%</h3>
                <p>Idéal pour plage</p>
            </div>
            
            <div class="activity-card">
                <h3><?php echo $activityScores['biking']; ?>%</h3>
                <p>Idéal pour vélo</p>
            </div>
        </div>
    </div>
    <?php else: ?>
    <p>Les recommandations d'activités ne sont pas disponibles.</p>
    <?php endif; ?>
</section>


<aside id='imageRandom' class="random-image">
    <?php if ($randomImage): ?>
        <figure>
            <img src="<?php echo htmlspecialchars($randomImage); ?>" alt="Image aléatoire sur la météo">
            <figcaption><?php echo htmlspecialchars($caption); ?></figcaption>
        </figure>
    <?php else: ?>
        <p>Aucune image disponible.</p>
    <?php endif; ?>
</aside>

<section class="navigation-buttons">
    <div class="button-container">
        <a href="previsions.php" class="nav-button">
            <span class="icon">🔍</span> Rechercher la météo par ville
        </a>
        
        <a href="statistiques.php" class="nav-button">
            <span class="icon">📊</span> Statistiques de consultation
        </a>
        
        <a href="tech.php" class="nav-button">
            <span class="icon">⚙</span> Page technique
        </a>
    </div>
</section>



<?php include("./include/footer.inc.php"); ?>