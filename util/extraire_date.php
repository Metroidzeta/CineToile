<?php
/**
 * Utilitaire : Extraire une date de la BDD (fonction extraireDate)
 * Version : v3.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */

function extraireDate(string $valeur): string {
	if (empty($valeur)) { return ''; }

	$mois_fr = [ 1 => // On commence à l'index 1
		'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'
	];

	// Vérifie que le format est bien correct
	if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $valeur)) { return ''; }

	[$annee, $mois, $jour] = explode('-', $valeur);

	$mois = (int) $mois;
	$jour = (int) $jour;

	if ($mois < 1 || $mois > 12) { return ''; }

	return "{$jour} {$mois_fr[$mois]} {$annee}";
}
?>