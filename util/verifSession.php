<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

session_start();
$connecte = false;

if(isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo'])
	AND isset($_SESSION['EMAIL']) && !empty($_SESSION['EMAIL'])) {
	$connecte = true;
}
?>