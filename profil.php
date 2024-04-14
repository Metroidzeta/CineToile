<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/page_interdite_si_non_connecte.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/afficher_realisateurs_avec_phrase.php';
require $racine . '/CineToile/util/extraire_metiers.php';

$valid_new_mdp = false;
$msgErr = '';

$nbFilms = executeReqFetch($dbh,'SELECT COUNT(*) AS nb_films FROM films')['nb_films'];
$nbIndividus = executeReqFetch($dbh,'SELECT COUNT(*) AS nb_individus FROM individus')['nb_individus'];

if($nbFilms > 0) {
	$id_film_aleatoire = rand(1,$nbFilms);
	$film = executeReqFetchArgs($dbh,'SELECT TITRE, AFFICHE FROM films WHERE id_film = ?',[$id_film_aleatoire]); // On récupére le film aléatoire
	// On récupére le nom du(ou des) réalisateur(s) associé(s) à ce film
	$realisateurs = executeReqFetchAllArgs($dbh,'SELECT individus.id_individu, NOM FROM individus INNER JOIN films_individus ON individus.id_individu = films_individus.id_individu WHERE id_film = ? AND role = "R"',[$id_film_aleatoire]);
}

if($nbIndividus > 0) {
	$id_individu_aleatoire = rand(1,$nbIndividus);
	$individu = executeReqFetchArgs($dbh,'SELECT NOM, METIERS, GENRE, PHOTO FROM individus WHERE id_individu = ?',[$id_individu_aleatoire]); // On récupére l'individu aléatoire
	$metiers = obtenirMetiers($individu['METIERS'],$individu['GENRE']); // On récupére le(s) métier(s) associé(s) à cet individu
}

if(isset($_POST['changer_mdp']) AND !empty($_POST['changer_mdp'])) { // On récupére les données du formulaire de changement de mot de passe
	$mdp_actuel = htmlspecialchars(trim($_POST['mdp_actuel'])); // On récupére le mot de passe actuel
	$new_mdp = htmlspecialchars(trim($_POST['new_mdp'])); // On récupére le nouveau mot de passe
	$new_mdp2 = htmlspecialchars(trim($_POST['new_mdp2'])); // On récupére la confirmation du nouveau mot du passe

	$compte = executeReqFetchArgs($dbh,'SELECT MOTDEPASSE, EMAIL FROM utilisateurs WHERE pseudo = ?',[$_SESSION['pseudo']]); // On récupére les données du compte

	if(strlen($new_mdp) < 4 || strlen($new_mdp) > 20) {
		$msgErr .= "- Le nouveau mot de passe doit contenir entre 4 et 20 caractères<br />";
	} else if(!password_verify($mdp_actuel,$compte['MOTDEPASSE'])) {
		$msgErr .= "- Le mot de passe actuel est incorrect<br />";
	} else if($new_mdp !== $new_mdp2) {
		$msgErr .= "- La confirmation du nouveau mot de passe ne correspond pas<br />";
	} else if($new_mdp === $compte['EMAIL']) { // Si le mdp = email
		$msgErr .= "- Le nouveau mot de passe doit être différent de l'e-mail<br />";
	}

	if(empty($msgErr)) { // Si le nouveau mot de passe est valide
		$new_mdp_hache = password_hash($new_mdp,PASSWORD_DEFAULT);
		executeReqArgs($dbh,'UPDATE utilisateurs SET MOTDEPASSE = ? WHERE pseudo = ?',[$new_mdp_hache,$_SESSION['pseudo']]);
		$valid_new_mdp = true;
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head><?php require $racine . '/CineToile/base/head.php'; ?></head>
	<body><?php require $racine . '/CineToile/base/barremenu.php'; ?>
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
					<form action="profil" method="POST">
						<div class="pt-1">
							<input type="password" name="mdp_actuel" class="form-control" placeholder="Mot de passe actuel" required="required" autocomplete="off"/>
						</div>
						<div class="pt-3">
							<input type="password" name="new_mdp" class="form-control" placeholder="Nouveau mot de passe" required="required" autocomplete="off"/>
						</div>
						<div class="pt-3">
							<input type="password" name="new_mdp2" class="form-control" placeholder="Confirmer le nouveau mot de passe" required="required" autocomplete="off"/>
						</div>
						<div class="pt-3 d-grid gap-2">
							<input type="submit" name="changer_mdp" class="btn btn-success" value="Changer mot de passe"/>
						</div>
					</form>

					<?php if($valid_new_mdp) { ?>
						<div class="alert alert-success"><b>Votre mot de passe a été modifié avec succès !</b></div>
					<?php } else if(!empty($msgErr)) { ?>
						<div class="alert alert-danger"><b><?= $msgErr ?></b></div>
					<?php } ?>

					<h4 class="pt-3">Désinscription</h4>
					<div class="d-grid gap-2">
						<a class="btn btn-danger text-white" href="avertissement_desinscription" role="button">Supprimer mon compte</a>
					</div>
				</div>

				<div class="col-10 offset-1 col-lg-6 offset-lg-0">
					<h2 id="recommandation">Recommandations (aléatoire)</h2>
					<div class="row">
						<div class="col-6">
							<?php if($nbFilms > 0) { ?>
								<div class="affiche">
									<a href="film?id=<?= $id_film_aleatoire ?>">
										<img class="img-fluid" src="img/affiches/<?= $film['AFFICHE'] ?>" alt="affiche <?= $film['TITRE'] ?>"/>
										<div class="overlay">
											<div class="titreAffiche text-uppercase"><?= $film['TITRE'] ?></div>
											<div class="soustitreAffiche"><?php realisateurs_avec_phrase($realisateurs); ?></div>
										</div>
									</a>
								</div>
							<?php } ?>
						</div>
						<div class="col-6">
							<?php if($nbIndividus > 0) { ?>
								<div class="affiche">
									<a href="individu?id=<?= $id_individu_aleatoire ?>">
										<img class="img-fluid" src="img/individus/<?= $individu['PHOTO'] ?>" alt="photo de <?= $individu['NOM'] ?>"/>
										<div class="overlay">
											<div class="titreAffiche"><?= $individu['NOM'] ?></div>
											<div class="soustitreAffiche"><?= $metiers ?></div>
										</div>
									</a>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php require $racine . '/CineToile/base/footer.php'; ?>
	</body>
</html>