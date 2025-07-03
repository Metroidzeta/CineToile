<?php
/**
 * Page home (/home)
 *
 * @version 4.0
 * @author Alain Barbier alias "Metroidzeta" (fullstack: backend/frontend)
 * @author Roger Huang (frontend/design)
 */

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/extraire_date.php';
require $racine . '/CineToile/util/afficher_realisateurs_avec_phrase.php';

require $racine . '/CineToile/models/HomeModel.php';

$model = new HomeModel($dbh);

$carouselItems = $model->getCarouselItems();
$films = $model->getFilms();
$articles = $model->getDerniersArticles();
$nbArticles = count($articles);

$view = $racine . '/CineToile/views/homeView.php';
require $racine . '/CineToile/views/homelayout.php';
?>