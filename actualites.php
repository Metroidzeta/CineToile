<?php
/**
 * Page des actualités (/actualites)
 *
 * @version 4.0
 * @author Alain Barbier alias "Metroidzeta" (fullstack: backend/frontend)
 * @author Roger Huang (frontend/design)
 */

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/extraire_date.php';

require $racine . '/CineToile/models/ActualitesModel.php';
$valid = false;

if (!isset($_GET['page']) || (ctype_digit($_GET['page']) && (int)$_GET['page'] > 0)) {
	$numPage = (int) ($_GET['page'] ?? 1);
	$model = new ActualitesModel($dbh);
	$nbArticles = $model->getNbArticles();
	$nbPages = (int) ceil($nbArticles / ActualitesModel::NB_ARTICLES_PAR_PAGE); // Arrondie à l'entier supérieur

	if ($nbArticles > 0 && $numPage <= $nbPages) {
		$articles = $model->getArticlesByPage($numPage);
		$startPage = max($numPage - 2, 1);
		$endPage = min($numPage + 2, $nbPages);
		$valid = true;
	} elseif ($nbArticles === 0 && $numPage === 1) {
		$articles = [];
		$startPage = $endPage = 1;
		$valid = true;
	}
}

$view = $racine . '/CineToile/views/actualitesView.php';
require $racine . '/CineToile/views/layout.php';
?>