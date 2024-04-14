<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';

if(isset($_POST['id']) && ctype_digit($_POST['id']) 
	AND isset($_POST['stars']) && filter_var($_POST['stars'],FILTER_VALIDATE_FLOAT) 
	AND $connecte) {
	$id = htmlspecialchars($_POST['id']);
	$nouvelle_note = htmlspecialchars($_POST['stars']);

	// On vérifie si l'avis de l'utilisateur existe déjà sur ce film
	$avis_existe = executeReqFetchArgs($dbh,'SELECT NOTE FROM films_avis WHERE id_film = ? AND pseudo = ?',[$id,$_SESSION['pseudo']]);

	if(!$avis_existe) { // On insére l'avis de l'utilisateur sur ce film
		executeReqArgs($dbh,'INSERT INTO films_avis (id_film,pseudo,NOTE,DATE_AVIS) VALUES(?,?,?,?)',[$id,$_SESSION['pseudo'],$nouvelle_note,date('Y-m-d')]);
	} else { // Sinon on modifie l'avis de l'utilisateur sur ce film	
		executeReqArgs($dbh,'UPDATE films_avis SET NOTE = ?, DATE_AVIS = ? WHERE id_film = ? AND pseudo = ?',[$nouvelle_note,date('Y-m-d'),$id,$_SESSION['pseudo']]);
	}

	$req_moyenne = executeReqFetchArgs($dbh,'SELECT AVG(NOTE) FROM films_avis WHERE id_film = ?',[$id]); // On récupére la moyenne des notes associées à ce film
	$req_nb_avis = executeReqFetchArgs($dbh,'SELECT COUNT(*) AS nb_avis FROM films_avis WHERE id_film = ?',[$id]); 	// On récupére le nombre d'avis associés à ce film
	// On récupére les 3 derniers avis associés à ce film
	$les_avis = executeReqFetchAllArgs($dbh,'SELECT utilisateurs.pseudo, NOTE, DATE_AVIS, CRITIQUE FROM films_avis INNER JOIN utilisateurs ON films_avis.pseudo = utilisateurs.pseudo WHERE id_film = ? ORDER BY DATE_AVIS DESC LIMIT 3',[$id]);

	$resultat[] = [
		'moyenne' => $req_moyenne['AVG(NOTE)'],
		'nb_avis' => $req_nb_avis['nb_avis']
	];

	foreach($les_avis as $avis) {
		$resultat[] = [
			'pseudo' => $avis['pseudo'],
			'NOTE' => $avis['NOTE'],
			'DATE_AVIS' => $avis['DATE_AVIS'],
			'CRITIQUE' => $avis['CRITIQUE']
		];
	}

	$premier_objet = $resultat[0];
	array_shift($resultat);
	$resultat_final = [$premier_objet,$resultat];

	header('Content-Type: application/json');
	echo json_encode($resultat_final);
}
?>