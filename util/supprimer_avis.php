<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/page_interdite_si_non_connecte.php';
require $racine . '/CineToile/util/connexionBDD.php';

if(isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$id = (int) $_GET['id'];
	// On vérifie si l'avis de l'utilisateur existe déjà sur ce film
	$avis_existe = executeReqFetchArgs($dbh,'SELECT NOTE FROM films_avis WHERE id_film = ? AND pseudo = ?',[$id,$_SESSION['pseudo']]);

	if($avis_existe) { // On supprime l'avis de l'utilisateur sur ce film
		executeReqArgs($dbh,'DELETE FROM films_avis WHERE id_film = ? AND pseudo = ?',[$id,$_SESSION['pseudo']]);
		header('Location:/CineToile/film?id=' . $id);
		die();
	}
}
header('Location:/CineToile/home');
die();
?>