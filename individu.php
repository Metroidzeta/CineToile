<?php
/**
 * Page d'un individu (/individu?id=X)
 *
 * @version 4.0
 * @author Alain Barbier alias "Metroidzeta" (fullstack: backend/frontend)
 * @author Roger Huang (frontend/design)
 */

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/extraire_metiers.php';
require $racine . '/CineToile/util/extraire_date.php';

require $racine . '/CineToile/models/IndividuModel.php';
$individu = false;

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$id = (int) $_GET['id'];
	$model = new IndividuModel($dbh);
	$individu = $model->getIndividu($id);

	if ($individu) {
		$dateNaissanceAffichee = extraireDate($individu['DATE_NAISSANCE']);
		$age = null;
		if (!empty($individu['DATE_NAISSANCE'])) $age = IndividuModel::calculerAge($individu['DATE_NAISSANCE']);

		$genre = IndividuModel::getGenre($individu['GENRE']);
		$films = $model->getFilmsIndividu($id); // On récupére la filmographie associée à cet individu
	}
}

$view = $racine . '/CineToile/views/individuView.php';
require $racine . '/CineToile/views/layout.php';
?>