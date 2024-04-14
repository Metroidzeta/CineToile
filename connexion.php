<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/page_interdite_si_connecte.php';
require $racine . '/CineToile/util/connexionBDD.php';

$msgErr = '';

if(isset($_POST['connexion']) && !empty($_POST['connexion'])) { // On récupére les informations du formulaire de connexion
	$email = htmlspecialchars(trim($_POST['email'])); // On récupére l'email
	$mdp = htmlspecialchars(trim($_POST['mdp'])); // On récupére le mot de passe

	$utilisateur = executeReqFetchArgs($dbh,'SELECT * FROM utilisateurs WHERE EMAIL = ?',[$email]); // On vérifie si cet email existe bien et on récupére les informations de l'utilisateur

	if(!$utilisateur) { // Si l'utilisateur n'existe pas (cet email n'existe pas)
		$msgErr .= "- Cet email n'existe pas<br />";
	} else if(!password_verify($mdp,$utilisateur['MOTDEPASSE'])) { // Si mdp incorrect
		$msgErr .= '- Le mot passe est incorrect<br />';
	} else { // Correct
		$_SESSION['pseudo'] = $utilisateur['pseudo'];
		$_SESSION['EMAIL'] = $utilisateur['EMAIL'];
		header('Location:/CineToile/profil');
		die();
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head><?php require $racine . '/CineToile/base/head.php'; ?></head>
	<body class="body_formulaire"><?php require $racine . '/CineToile/base/barremenu.php'; ?>
		<div class="container">
			<div id="formulaire" class="text-center">
				<h2>Connexion</h2>
				<form action="connexion" method="POST">
					<div class="mb-3">
						<input type="email" name="email" class="form-control" placeholder="Email" value="<?php if(!empty($msgErr)) { echo $email; } ?>" required="required" autocomplete="off"/>
					</div>
					<div class="mb-3">
						<input type="password" name="mdp" class="form-control" placeholder="Mot de passe" required="required" autocomplete="off"/>
					</div>
					<div class="mb-3">
						<input type="submit" name="connexion" class="btn btn-danger" value="Se connecter"/>
					</div>
				</form>
				<p>Pas encore inscris ? <a class="text-danger" href="inscription">Incrivez-vous!</a></p>
				<?php if(!empty($msgErr)) { ?>
					<div class="alert alert-danger"><?= $msgErr ?></div>
				<?php } ?>
			</div>
		</div>
    </body>
</html>