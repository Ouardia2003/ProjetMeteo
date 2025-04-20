<?php
/**
 * @file footer.inc.php
 *
 * @author Lisa/Ouardia
 * 
 * @brief Ce fichier contient le pied de page du site, avec les sections Aide, Informations, et Organisme.
 * Il affiche également le nombre de visites via la fonction `updateHits()` et la date du jour.
 *
 * PHP version 8.1+
 * 
 */
require_once("./include/functions.inc.php");
?>
<footer>
    <div class="foot">
        <div class="aide">
            <span>Aide ?</span>
            <ul>
                <li>
                    Réalisé par Ouardia ACHAB - Lisa ISSAD ®
                </li>
                <li>
                    <a href="apropos.php">À propos de nous</a>
                </li>
            </ul>
        </div>
        <div class="infos">
            <span>Informations</span>
            <ul>
                <li>
                    <a href="tech.php">Tech</a>
                </li>
                <li>
                    <a href="plan.php">Plan du Site</a>
                </li>
                <li>
                    <span>Hits: </span><span class="word"><?= updateHits(); ?></span> <!-- Afficher le nombre de visites sur la même ligne -->
                </li>
            </ul>
        </div>
        <div class="organisme">
            <span>Organisme</span>
            <ul>
                <li>
                    CY Cergy Paris Université © 2025
                </li>
                <li>
                    Mis à jour le : <?= date("d/m/Y") ?>
                </li>
            </ul>
        </div>
    </div>
</footer>
</body>
</html>
