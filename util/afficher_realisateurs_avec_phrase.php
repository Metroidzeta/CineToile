<?php
/**
 * Utilitaire : realisateurs_avec_phrase (obtenir les réalisateurs sous forme d'une phrase "Réalisé par .. (et ..)")
 * Version : v3.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */

function realisateurs_avec_phrase($realisateurs) : string {
	if (empty($realisateurs)) return '';

	$noms = explode(', ', $realisateurs);
	$count = count($noms);

	if ($count === 1) return 'Réalisé par ' . $noms[0];
	if ($count === 2) return 'Réalisé par ' . $noms[0] . ' et ' . $noms[1];

	$dernier = array_pop($noms);
	return 'Réalisé par ' . implode(', ', $noms) . ' et ' . $dernier;
}
?>