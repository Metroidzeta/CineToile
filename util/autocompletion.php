<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/connexionBDD.php';

if(isset($_GET['rechercher']) && !empty($_GET['rechercher']) && !ctype_space($_GET['rechercher'])) {
	$recherche = htmlspecialchars($_GET['rechercher']);
	$recherche = mb_strtoupper($recherche); // mettre en majuscules
	$recherche = str_replace(' ','',$recherche); // supprimer les espaces
	$recherche_sql = "%{$recherche}%";

	$films = executeReqFetchAllArgs($dbh,'SELECT TITRE FROM films WHERE UPPER(REPLACE(TITRE," ","")) LIKE ?',[$recherche_sql]);
	$individus = executeReqFetchAllArgs($dbh,'SELECT NOM FROM individus WHERE UPPER(REPLACE(NOM," ","")) LIKE ?',[$recherche_sql]);

	$resultat = [];
	foreach($films as $film) {
		$resultat[] = [
			'value' => mb_strtoupper($film['TITRE']),
			'label' => mb_strtoupper($film['TITRE'])
		];
	}

	foreach($individus as $individu) {
		$resultat[] = [
			'value' => $individu['NOM'],
			'label' => $individu['NOM']
		];
	}

	header('Content-Type: application/json');
	echo json_encode($resultat);
}
?>