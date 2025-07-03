<?php
/**
 * Page de connexion (/connexion)
 *
 * @version 4.0
 * @author Alain Barbier alias "Metroidzeta" (fullstack: backend/frontend)
 * @author Roger Huang (frontend/design)
 */

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/page_interdite_si_connecte.php';
require $racine . '/CineToile/util/connexionBDD.php';

require $racine . '/CineToile/models/ConnexionModel.php';
$msgErr = '';

// Génération du token CSRF s'il n'existe pas déjà
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Vérification du token CSRF
	if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
		die('Erreur CSRF : formulaire invalide ou expiré.');
	}

	$email = trim($_POST['email'] ?? ''); // On récupére l'email
	$password = trim($_POST['password'] ?? ''); // On récupére le mot de passe

	if ($email === '' || $password === '') {
		$msgErr = 'Tous les champs sont requis';
	}
	else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$msgErr = 'Email invalide';
	} else {
		$model = new ConnexionModel($dbh);
		$user = $model->getUtilisateurDepuisEmail($email);
		if (!$user || !password_verify($password, $user['MOTDEPASSE'])) { // email ou password incorrect
            $msgErr = 'Email ou mot de passe incorrect';
        } else { // Connexion réussie : stockage en session
			session_regenerate_id(true);
			$_SESSION['pseudo'] = $user['pseudo'];
			$_SESSION['EMAIL'] = $user['EMAIL'];
			$_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Régénérer un nouveau token CSRF pour la session
			header('Location:/CineToile/profil');
			exit;
		}
	}
}

$view = $racine . '/CineToile/views/connexionView.php';
require $racine . '/CineToile/views/layout.php';
?>