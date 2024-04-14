<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

function realisateurs_avec_phrase($realisateurs) {
	$nomsRealisateurs = array_column($realisateurs,'NOM');
	if(!empty($nomsRealisateurs)) {
		echo 'Réalisé par ' . implode(' et ',$nomsRealisateurs);
	}
}
?>