<?php
/**
 * @file plan.php
 * 
 *  @author  Lisa/Ouardia 
 * @brief Ce fichier génère dynamiquement le plan du site en listant tous les fichiers PHP présents dans le répertoire courant. 
 * Chaque fichier est affiché avec son titre récupéré via la fonction getPageTitle.
 * 
 *  PHP version 8.1+
 *
 */

require_once("./include/functions.inc.php"); // Inclusion des fonctions utilitaires

// Définition des métadonnées de la page
$pageTitle = "Plan du Site";
$pageDescription = "Page plan du site web Ma Météo - projet L2-Informatique S2 - Développement Web";

// Inclusion de l'en-tête HTML commun à toutes les pages
include("./include/header.inc.php");
?>

<main>
    <h1>Plan du site</h1>
    <section class="plan-du-site">
        <h2>Sommaire</h2>
        <ul>
            <?php
            /**
             * Récupération de tous les fichiers PHP dans le répertoire courant,
             * triés par ordre alphabétique, puis affichés en tant que liens HTML
             * avec leur titre récupéré grâce à getPageTitle().
             */
            $files = getPHPFiles('.');  // Récupère tous les fichiers PHP du répertoire
            sort($files);               // Trie les fichiers par ordre alphabétique

            foreach ($files as $file) {
                $title = getPageTitle($file); // Récupère le titre de la page
                echo '<li><a href="' . $file . '">' . $title . '</a></li>';
            }
            ?>
        </ul>
    </section>
</main>

<?php
// Inclusion du pied de page HTML
include("./include/footer.inc.php");
?>