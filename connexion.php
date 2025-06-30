<?php
/**
 * Page : Affichage de la page connexion (/connexion)
 * Version : v3.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/page_interdite_si_connecte.php';
require $racine . '/CineToile/util/connexionBDD.php';

$msgErr = '';

if (isset($_POST['connexion']) && !empty($_POST['connexion'])) {
	$email = htmlspecialchars(trim($_POST['email'])); // On récupére l'email
	$password = htmlspecialchars(trim($_POST['password'])); // On récupére le mot de passe

	$user = executeReqFetchArgs($dbh, // On récupére les informations de l'utilisateur
		'SELECT *
		FROM utilisateurs
		WHERE EMAIL = ?',[$email]
	);

	if (!$user) { // Si l'utilisateur n'existe pas (cet email n'existe pas)
		$msgErr .= "- Cet email n'existe pas<br />";
	} else if (!password_verify($password, $user['MOTDEPASSE'])) { // Si mdp incorrect
		$msgErr .= '- Le mot passe est incorrect<br />';
	} else { // Correct
		$_SESSION['pseudo'] = $user['pseudo'];
		$_SESSION['EMAIL'] = $user['EMAIL'];
		header('Location:/CineToile/profil');
		die();
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php require $racine . '/CineToile/base/head.php'; ?>
	</head>
	<body class="body_formulaire">
		<?php require $racine . '/CineToile/base/barremenu.php'; ?>

		<div class="container">
			<div id="formulaire" class="text-center">
				<h2>Connexion</h2>
				<form action="connexion" method="POST" autocomplete="off">
					<div class="mb-3">
						<input type="email" name="email" class="form-control" placeholder="Email" value="<?= !empty($msgErr) ? $email : '' ?>" required/>
					</div>
					<div class="mb-3">
						<input type="password" name="password" class="form-control" placeholder="Mot de passe" required/>
					</div>
					<div class="mb-3">
						<input type="submit" name="connexion" class="btn btn-danger" value="Se connecter"/>
					</div>
				</form>
				<p>Pas encore inscris ? <a class="text-danger" href="inscription">Incrivez-vous!</a></p>
				<?php if(!empty($msgErr)): ?>
					<div class="alert alert-danger"><?= $msgErr ?></div>
				<?php endif; ?>
			</div>
		</div>
    </body>
</html>