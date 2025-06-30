<?php
/**
 * Utilitaire : getStars
 * Version : v3.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */

function getStars($nombre, $taillePx) : string {
	if ($taillePx == null) { return ''; }
	$resultat = '';
	for ($i = 0; $i < 5; $i++) {
		$starType = 'black_star.png';
		if ($nombre != null) {
			$starType = $nombre > $i ? ($nombre < $i + 1 ? 'half_star.png' : 'gold_star.png') : 'black_star.png';
		}
		$resultat .= '<img class="img-fluid" style="height: ' . $taillePx . 'px;" src="img/etoiles/' . $starType . '" alt="' . $starType . '"/> ';
	}
	return $resultat;
}
?>