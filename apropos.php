<?php

/**

 * @file apropos.php

 * @author Ouardia/Lisa
 * @brief Page À Propos du site "Ma Météo".

 *

 * Cette page présente les développeuses du site, les objectifs du projet, les avantages proposés par l'application, et fournit un moyen de contact.

 *

 *PHP version 8.1+

 * 

 */

// Définition du titre et de la description de la page
$pageTitle = "À Propos De Nous";
$pageDescription = "Découvrez qui nous sommes et les objectifs de notre projet.";

// Inclusion du header
include("./include/header.inc.php");
?>
<h1>Bienvenue sur notre site Ma Météo. Merci pour votre visite !</h1>
<!-- Section de présentation des créatrices -->

<section class="about-us">
    <h2>Qui sommes-nous ?</h2>
    <p>
        Nous sommes <strong>Ouardia ACHAB</strong> et <strong>Lisa ISSAD</strong>, étudiantes à 
        <strong>CY Cergy Paris Université</strong>. Passionnées par le développement web et les technologies 
        de l'information, nous avons réalisé ce projet dans le cadre de nos études pour mettre en pratique 
        nos compétences en programmation et en gestion d'API.
    </p>
</section>
<!-- Section des avantages du site -->

<section class="advantages">
    <h2>Les avantages de <em>Ma Météo</em></h2>
    <p>
    Ce site vous propose une expérience complète et pratique pour consulter la météo au quotidien
     </p>   
    <ul>
        <li><strong>Accès rapide aux prévisions météo</strong> pour toutes les villes en France .</li>
        <li><strong>Mise à jour en temps réel</strong> des conditions météorologiques : température.</li>
        <li><strong>Affichage clair et synthétique</strong> des prévisions journalières et hebdomadaires.</li>
        <li><strong>Localisation rapide </strong> Trouvez la météo de votre ville en quelques clics grâce à notre outil de recherche pratique.</li>
        <li><strong>Recherche simplifiée par nom de ville</strong> pour une utilisation fluide et rapide.</li>
    </ul>
</section>
<!-- Section de contact -->

<section class="contact">
    <h2>Nous contacter</h2>
    <p>
        Vous avez des suggestions pour améliorer <em>Ma Météo</em> ? N'hésitez pas à nous écrire :
    </p>
    <ul>
        <li><strong>LinkedIn :</strong>Lisa ISSAD |Ouardia ACHAB </li>
    </ul>
</section>


<?php include("./include/footer.inc.php"); ?>