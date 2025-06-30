<?php
/**
 * Model : Modèle de la page "individu"
 * Version : v4.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */
 
function getIndividu(PDO $dbh, int $id) {
	return executeReqFetchArgs($dbh, 'SELECT * FROM individus WHERE id_individu = ?', [$id]);
}

function getFilmsIndividu(PDO $dbh, int $id) {
	return executeReqFetchAllArgs($dbh,
		'SELECT DISTINCT f.id_film, f.TITRE, f.AFFICHE
		FROM films f
		INNER JOIN films_individus fi ON f.id_film = fi.id_film
		WHERE fi.id_individu = ?', [$id]
	);
}

function getGenre(string $code) : string {
	return $code === 'H' ? 'Homme' : ($code === 'F' ? 'Femme' : '');
}

function calculerAge(string $dateNaissance) : ?int {
	if (!$dateNaissance) return null;
	$dt = new DateTime($dateNaissance);
	$now = new DateTime();
	return $dt->diff($now)->y;
}
?>