<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/page_interdite_si_non_connecte.php';
require $racine . '/CineToile/util/connexionBDD.php';

executeReqArgs($dbh,'DELETE FROM films_avis WHERE pseudo = ?',[$_SESSION['pseudo']]); // On supprime tous les avis accociés à cet utilisateur
executeReqArgs($dbh,'DELETE FROM utilisateurs WHERE pseudo = ?',[$_SESSION['pseudo']]); // On supprime cet utilisateur

header('Location:/CineToile/util/deconnexion');
die();
?>