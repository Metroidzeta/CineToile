<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

function obtenir_etoiles($nombre,$taillePx) {
	$resultat = '';
	if($taillePx == null) { return $resultat; }
	for($i = 0; $i < 5; $i++) {
		if($nombre != null) {
			$starType = $nombre > $i ? ($nombre < $i + 1 ? 'half_star.png' : 'gold_star.png') : 'black_star.png';
		} else {
			$starType = 'black_star.png';
		}
		$resultat .= '<img class="img-fluid" style="height: ' . $taillePx . 'px;" src="img/etoiles/' . $starType . '" alt="' . $starType . '"/> ';
	}
	return $resultat;
}
?>