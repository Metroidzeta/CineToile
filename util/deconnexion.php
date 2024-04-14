<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';

if($connecte) {
	unset($_SESSION['pseudo'],$_SESSION['EMAIL']);
	session_destroy();
}
header('Location:/CineToile/home');
die();
?>