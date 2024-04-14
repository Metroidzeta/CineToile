<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

function obtenirMetiers($metiers, $genre) {
	if(empty($metiers)) { return ''; }

	$metiersToString = [
		'R' => ($genre === 'F') ? 'Réalisatrice ' : 'Réalisateur ',
		'A' => ($genre === 'F') ? 'Actrice ' : 'Acteur '
	];

	$resultat = strtr($metiers,$metiersToString);

    return rtrim($resultat); // supprime l'espace en fin de chaine
}
?>