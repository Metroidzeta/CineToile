<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

function obtenirDate($valeur) {
	if(empty($valeur)) { return ''; }

	// Le tableau des mois pour transformer le mois d'une date provenant de la BDD sous forme de texte en français
	$tab_mois_fr = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];

	[$annee, $mois, $jour] = explode('-',$valeur);
	return (int) $jour . ' ' . $tab_mois_fr[(int) $mois - 1] . ' ' . $annee;
}
?>