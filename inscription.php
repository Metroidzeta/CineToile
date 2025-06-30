<?php
/**
 * Page : Affichage de la page inscription (/inscription)
 * Version : v3.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/page_interdite_si_connecte.php';
require $racine . '/CineToile/util/connexionBDD.php';

$valid = false;
$msgErr = '';

if (isset($_POST['inscription']) && !empty($_POST['inscription'])) { // On récupère les informations du formulaire d'inscription
	$pseudo = htmlspecialchars(trim($_POST['pseudo'])); // On récupère le pseudo
	$email = htmlspecialchars(trim($_POST['email'])); // On récupère l'email
	$password = htmlspecialchars(trim($_POST['password'])); // On récupère le mdp
	$password2 = htmlspecialchars(trim($_POST['password2'])); // On récupère la confirmation du mdp

	$pseudo_exist = executeReqFetchArgs($dbh,
		'SELECT PSEUDO
		FROM utilisateurs
		WHERE PSEUDO = ?',[$pseudo]
	);

	// Vérification du pseudo
	if ($pseudo_exist) {
		$msgErr .= "- Ce pseudo est déjà pris<br/>";
	} else if (strlen($pseudo) < 4 || strlen($pseudo) > 20) {
		$msgErr .= "- Le pseudo doit contenir entre 4 et 20 caractères<br/>";
	} else if (!preg_match('/^[a-zA-Z0-9]+$/', $pseudo)) {
		$msgErr .= "- Ce pseudo n'est pas valide<br/>";
	}

	// Vérification du mot de passe
	if ($password === $email) {
		$msgErr .= "- Le mot de passe doit être différent de l'e-mail<br/>";
	} else if (strlen($password) < 4 || strlen($password) > 20) {
		$msgErr .= "- Le mot de passe doit contenir entre 4 et 20 caractères<br/>";
	} else if ($password !== $password2) {
		$msgErr .= "- La confirmation du mot de passe ne correspond pas<br/>";
	}

	$email_exist = executeReqFetchArgs($dbh,
		'SELECT EMAIL
		FROM utilisateurs
		WHERE EMAIL = ?',[$email]
	);

	// Vérification de l'email
	if ($email_exist) {
		$msgErr .= "- Cet e-mail est déjà utilisé<br/>";
	} else if (strlen($email) > 50) {
		$msgErr .= "- L'e-mail ne peut pas dépasser 50 caractères<br />";
	} else if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("^[_a-zA-Z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email)) {
		$msgErr .= "- L'e-mail n'est pas valide<br />";
	}

	if (empty($msgErr)) { // Si l'inscription est valide
		$password_hache = password_hash($password, PASSWORD_DEFAULT);

		executeReqArgs($dbh, // On insère le nouvel utilisateur dans la BDD
			'INSERT INTO utilisateurs (PSEUDO,MOTDEPASSE,EMAIL)
			VALUES(?,?,?)',[$pseudo, $password_hache, $email]
		);

		$valid = true;
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
				<h2>Inscription</h2>
				<form action="inscription" method="POST" autocomplete="off">
					<div class="mb-3">
						<input type="text" name="pseudo" class="form-control" placeholder="Pseudo" value="<?= !empty($msgErr) ? $pseudo : '' ?>" minlength="4" maxlength="20" required/>
					</div>
					<div class="mb-3">
						<input type="email" name="email" class="form-control" placeholder="Email" value="<?= !empty($msgErr) ? $email : '' ?>" maxlength="50" required/>
					</div>
					<div class="mb-3">
						<input type="password" name="password" class="form-control" placeholder="Mot de passe" minlength="4" maxlength="20" required/>
					</div>
					<div class="mb-3">
						<input type="password" name="password2" class="form-control" placeholder="Confirmer le mot de passe" minlength="4" maxlength="20" required/>
					</div>
					<div class="mb-3">
						<input type="submit" name="inscription" class="btn btn-danger" value="S'inscrire"/>
					</div>
				</form>
				<?php if($valid): ?>
					<div class="alert alert-success">Votre inscription a bien été prise en compte !</div>
				<?php elseif(!empty($msgErr)): ?>
					<div class="alert alert-danger"><?= $msgErr ?></div>
				<?php endif; ?>
			</div>
		</div>
	</body>
</html>