<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/page_interdite_si_non_connecte.php';
require $racine . '/CineToile/util/connexionBDD.php';

$film = false;

if(isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$id = (int) $_GET['id']; // On récupére l'id du film
	$film = executeReqFetchArgs($dbh,'SELECT TITRE FROM films WHERE id_film = ?',[$id]); // On récupére le titre du film

	if($film) {
		// On récupére l'avis (si il existe) et la note de l'utilisateur sur ce film
		$donnees_avis = executeReqFetchArgs($dbh,'SELECT NOTE, CRITIQUE FROM films_avis INNER JOIN utilisateurs ON films_avis.pseudo = utilisateurs.pseudo WHERE id_film = ? AND films_avis.pseudo = ?',[$id,$_SESSION['pseudo']]);
	}
}

if(isset($_POST['envoyer']) AND !empty($_POST['envoyer'])) { // On récupére l'avis de l'utilisateur
	$nouvelle_note = htmlspecialchars($_POST['rating']); // On récupére la note de l'utilisateur
	$nouvelle_note /= 2;
	$critique = htmlspecialchars($_POST['critique']); // On récupére la critique de l'utilisateur
	// On insére l'avis de l'utilisateur sur ce film
	executeReqArgs($dbh,'UPDATE films_avis SET NOTE = ?, DATE_AVIS = ?, CRITIQUE = ? WHERE id_film = ? AND pseudo = ?',[$nouvelle_note,date('Y-m-d'),$critique,$id,$_SESSION['pseudo']]);

	header('Location:/CineToile/film?id=' . $id);
	die();
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head><?php require $racine . '/CineToile/base/head.php'; ?>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const rateInputs = document.querySelectorAll('.rate input');

				rateInputs.forEach(function(input) {
					input.addEventListener('click', function() {
						 document.getElementById('note_utilisateur').innerHTML = this.value / 2;
					});
				});
			});
		</script>
	</head>
	<body class="bg-dark"><?php require $racine . '/CineToile/base/barremenu.php'; ?>
		<div class="container bg-white text-center" style="min-height:100vh;">
			<?php if($film) {
				if($donnees_avis) { ?>
					<h5>Vous pouvez modifier votre note sur ce film :</h5>
					<form method="POST" action="film_rediger_avis?id=<?php echo $id; ?>">
						<fieldset class="rate">
							<?php for($i = 10; $i > 0; $i--) { ?>
								<input type="radio" id="rating<?= $i ?>" name="rating" value="<?= $i ?>" <?php if($i == $donnees_avis['NOTE'] * 2) { echo ' checked'; } ?>/>
								<label <?php if($i % 2 != 0) { echo 'class="half"'; } ?> for="rating<?= $i ?>" title="<?php echo intdiv($i,2); if($i % 2 != 0) { echo '.5'; } echo ' étoile'; if($i > 3) { echo 's'; } ?>"></label>
							<?php } ?>
						</fieldset>
						<span id="note_utilisateur"><?= $donnees_avis['NOTE'] ?></span>

						<h5>Vous pouvez également rédiger une critique sur le film "<?= $film['TITRE'] ?>" :</h5>
						<div class="pt-3">
							<textarea name="critique" class="responsive-textarea" rows="10" cols="60" maxlength="400"><?php if(!empty($donnees_avis['CRITIQUE'])) { echo $donnees_avis['CRITIQUE']; } ?></textarea>
						</div>
						<div class="row pt-4">
							<div class="col-5 offset-1 col-md-3 offset-md-3">
								<input type="submit" name="envoyer" class="btn btn-primary" value="<?php if(empty($donnees_avis['CRITIQUE'])) { echo 'Envoyer'; } else { echo 'Modifier'; } ?>"/>
							</div>
							<div class="col-5 col-md-3">
								<a class="btn btn-danger text-white" href="util/supprimer_avis?id=<?= $id ?>" role="button">Supprimer</a>		
							</div>
						</div>
					</form>
				<?php } else { ?>
					<h5>Vous n'avez pas encore noté ce film, par conséquent vous ne pouvez pas rédiger votre critique.</h5>
				<?php } ?>
				<div class="pt-4">
					<a href="film?id=<?= $id ?>">« Retour</a>
				</div>
			<?php } else { ?>
				<h2>Erreur : Mauvais ID film ou film inexistant</h2>
			<?php } ?>
		</div>
	</body>
</html>