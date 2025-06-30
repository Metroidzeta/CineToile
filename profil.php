<?php
/**
 * Page : Affichage de la page du profil de l'utilisateur connecté (/profil)
 * Version : v3.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/page_interdite_si_non_connecte.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/afficher_realisateurs_avec_phrase.php';
require $racine . '/CineToile/util/extraire_metiers.php';

$valid_new_password = false;
$msgErr = '';

$nbFilms = (int) executeReqFetchColumn($dbh,
				'SELECT COUNT(*) FROM films'
			);

$nbIndividus = (int) executeReqFetchColumn($dbh,
				'SELECT COUNT(*) FROM individus'
			);

if ($nbFilms > 0) {
	$id_random_film = rand(1, $nbFilms);

	$film = executeReqFetchArgs($dbh, // On récupére le film aléatoire
		'SELECT TITRE, AFFICHE FROM films
		WHERE id_film = ?',[$id_random_film]
	);

	$realisateurs = executeReqFetchAllArgs($dbh, // On récupére le nom du (ou des) réalisateur(s) associé(s) à ce film
		'SELECT i.id_individu, i.NOM FROM individus i
		INNER JOIN films_individus fi ON i.id_individu = fi.id_individu
		WHERE fi.id_film = ? AND fi.role = "R"',[$id_random_film]
	);
}

if ($nbIndividus > 0) {
	$id_random_individu = rand(1, $nbIndividus);

	$individu = executeReqFetchArgs($dbh, // On récupére l'individu aléatoire
		'SELECT NOM, METIERS, GENRE, PHOTO FROM individus
		WHERE id_individu = ?',[$id_random_individu]
	);
}

if (isset($_POST['change_password']) && !empty($_POST['change_password'])) { // On récupére les données du formulaire de changement de mot de passe
	$mdp_actuel = htmlspecialchars(trim($_POST['mdp_actuel'])); // Le mot de passe actuel
	$new_password = htmlspecialchars(trim($_POST['new_password'])); // Le nouveau mot de passe
	$new_password2 = htmlspecialchars(trim($_POST['new_password2'])); // La confirmation du nouveau mot du passe

	$compte = executeReqFetchArgs($dbh, // On récupére les données du compte
		'SELECT MOTDEPASSE, EMAIL FROM utilisateurs
		WHERE pseudo = ?',[$_SESSION['pseudo']]
	);

	if (strlen($new_password) < 4 || strlen($new_password) > 20) {
		$msgErr .= "- Le nouveau mot de passe doit contenir entre 4 et 20 caractères<br />";
	} else if (!password_verify($mdp_actuel, $compte['MOTDEPASSE'])) {
		$msgErr .= "- Le mot de passe actuel est incorrect<br />";
	} else if ($new_password !== $new_password2) {
		$msgErr .= "- La confirmation du nouveau mot de passe ne correspond pas<br />";
	} else if ($new_password === $compte['EMAIL']) {
		$msgErr .= "- Le nouveau mot de passe doit être différent de l'e-mail<br />";
	}

	if (empty($msgErr)) { // Si le nouveau mot de passe est valide
		$new_password_hache = password_hash($new_password, PASSWORD_DEFAULT);

		executeReqArgs($dbh, // On met à jour le mot de passe du compte
			'UPDATE utilisateurs SET MOTDEPASSE = ?
			WHERE pseudo = ?',[$new_password_hache, $_SESSION['pseudo']]
		);

		$valid_new_password = true;
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php require $racine . '/CineToile/base/head.php'; ?>
	</head>
	<body>
		<?php require $racine . '/CineToile/base/barremenu.php'; ?>

		<div class="container">
			<div class="row">
				<div class="col-4 offset-4 col-sm-4 offset-sm-4 col-md-2 offset-md-5">
					<img src="img/profil/clapperboard.png" class="img-fluid" alt="image clapperboard"/>
				</div>
			</div>

			<h2 class="text-center">Votre Profil</h2>
			<div class="row pt-5">
				<div class="col-10 offset-1 col-lg-4 offset-lg-1">
					<h2>Vos informations :</h2>
					<h5>Pseudo : <?= $_SESSION['pseudo'] ?></h5>
					<h5>Adresse e-mail : <?= $_SESSION['EMAIL'] ?></h5>

					<h4>Changer mon mot de passe :</h4>
					<form action="profil" method="POST" autocomplete="off">
						<div class="pt-1">
							<input type="password" name="mdp_actuel" class="form-control" placeholder="Mot de passe actuel" minlength="4" maxlength="20" required/>
						</div>
						<div class="pt-3">
							<input type="password" name="new_password" class="form-control" placeholder="Nouveau mot de passe" minlength="4" maxlength="20" required/>
						</div>
						<div class="pt-3">
							<input type="password" name="new_password2" class="form-control" placeholder="Confirmer le nouveau mot de passe" minlength="4" maxlength="20" required/>
						</div>
						<div class="pt-3 d-grid gap-2">
							<input type="submit" name="change_password" class="btn btn-success" value="Changer mot de passe"/>
						</div>
					</form>

					<?php if ($valid_new_password): ?>
						<div class="alert alert-success"><b>Votre mot de passe a été modifié avec succès !</b></div>
					<?php elseif (!empty($msgErr)): ?>
						<div class="alert alert-danger"><b><?= $msgErr ?></b></div>
					<?php endif; ?>

					<h4 class="pt-3">Désinscription</h4>
					<div class="d-grid gap-2">
						<a class="btn btn-danger text-white" href="avertissement_desinscription" role="button">Supprimer mon compte</a>
					</div>
				</div>

				<div class="col-10 offset-1 col-lg-6 offset-lg-0">
					<h2 id="recommandation">Recommandations (aléatoire)</h2>
					<div class="row">
						<div class="col-6">
							<?php if($nbFilms > 0): ?>
								<div class="affiche">
									<a href="film?id=<?= $id_random_film ?>">
										<img class="img-fluid" src="/CineToile/img/affiches/<?= $film['AFFICHE'] ?>" alt="affiche <?= htmlspecialchars($film['TITRE']) ?>"/>
										<div class="overlay">
											<div class="titreAffiche text-uppercase"><?= htmlspecialchars($film['TITRE']) ?></div>
											<div class="soustitreAffiche"><?= realisateurs_avec_phrase($realisateurs) ?></div>
										</div>
									</a>
								</div>
							<?php endif; ?>
						</div>
						<div class="col-6">
							<?php if($nbIndividus > 0): ?>
								<div class="affiche">
									<a href="individu?id=<?= $id_random_individu ?>">
										<img class="img-fluid" src="/CineToile/img/individus/<?= $individu['PHOTO'] ?>" alt="photo de <?= htmlspecialchars($individu['NOM']) ?>"/>
										<div class="overlay">
											<div class="titreAffiche"><?= htmlspecialchars($individu['NOM']) ?></div>
											<div class="soustitreAffiche"><?= extraireMetiers($individu['METIERS'], $individu['GENRE']) ?></div>
										</div>
									</a>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php require $racine . '/CineToile/base/footer.php'; ?>
	</body>
</html>