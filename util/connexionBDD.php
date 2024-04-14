<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$nom_BDD = "cinema";
$identifiant = "root";
$mdp = "";

try {
	$dbh = new PDO('mysql:host=localhost;dbname='. $nom_BDD .';charset=utf8',$identifiant,$mdp);
} catch (Exception $e) {
	die('Erreur de connexion BDD : ' . $e->getMessage());
}

//$dbh->query("SET NAMES UTF8");

function executeReqArgs($db,$req,$args) {
	$requete = $db->prepare($req);
	$requete->execute($args);
	$requete->closeCursor();
}

function executeReqFetch($db,$req) {
	$requete = $db->prepare($req);
	$requete->execute();
	$data = $requete->fetch();
	$requete->closeCursor();
	return $data;
}

function executeReqFetchArgs($db,$req,$args) {
	$requete = $db->prepare($req);
	$requete->execute($args);
	$data = $requete->fetch();
	$requete->closeCursor();
	return $data;
}

function executeReqFetchAll($db,$req) {
	$requete = $db->prepare($req);
	$requete->execute();
	$data = $requete->fetchAll();
	$requete->closeCursor();
	return $data;
}

function executeReqFetchAllArgs($db,$req,$args) {
	$requete = $db->prepare($req);
	$requete->execute($args);
	$data = $requete->fetchAll();
	$requete->closeCursor();
	return $data;
}

function executeReqFetchAllArgsLimitOffset($db,$req,$args) {
	$requete = $db->prepare($req);
	foreach($args as $key => $value) {
		$requete->bindValue($key,$value,PDO::PARAM_INT);
	}
	$requete->execute();
	$data = $requete->fetchAll();
	$requete->closeCursor();
	return $data;
}

function executeReqFetchAllArgsLimitOffsetFirstSTR($db,$req,$args) {
	$requete = $db->prepare($req);
	$premier = true;
	foreach($args as $key => $value) {
		if($premier) { 
			$requete->bindValue($key,$value,PDO::PARAM_STR);
			$premier = false;
		} else {
			$requete->bindValue($key,$value,PDO::PARAM_INT);
		}
	}
	$requete->execute();
	$data = $requete->fetchAll();
	$requete->closeCursor();
	return $data;
}
?>