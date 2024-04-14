<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/extraire_metiers.php';
require $racine . '/CineToile/util/extraire_date.php';

$individu = false;

if(isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$id = (int) $_GET['id']; // On récupére l'id de l'individu venant de GET
	$individu = executeReqFetchArgs($dbh,'SELECT * FROM individus WHERE id_individu = ?',[$id]); // On récupére les données de cet individu

	if($individu) {
		$metiers = obtenirMetiers($individu['METIERS'],$individu['GENRE']);
		$date_naissance = obtenirDate($individu['DATE_NAISSANCE']);
		if(!empty($date_naissance)) {
			$age = date('Y') - date('Y',strtotime($individu['DATE_NAISSANCE']));
			if(date('md') < date('md',strtotime($individu['DATE_NAISSANCE']))) {
				$age--;
			}
		}

		$genre = '';
		if(!empty($individu['GENRE'])) {
			$genre = $individu['GENRE'];
			$genre = ($genre === 'H') ? 'Homme' : (($genre === 'F') ? 'Femme' : '');
		}

		// On récupére la filmographie associée à cet individu
		$films = executeReqFetchAllArgs($dbh,'SELECT DISTINCT films.id_film, TITRE, AFFICHE FROM films INNER JOIN films_individus ON films.id_film = films_individus.id_film WHERE id_individu = ?',[$id]);
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head><?php require $racine . '/CineToile/base/head.php'; ?></head>
	<body class="bg-dark"><?php require $racine . '/CineToile/base/barremenu.php'; ?>

		<div class="container bg-white" style="min-height:100vh;">
			<?php if($individu) { ?>
				<div class="row">
					<!-- Photo de l'individu -->
					<div class="text-center col-lg-3 col-md-8 offset-md-2 col-10 offset-1">
						<img class="img-fluid" src="img/individus/<?= $individu['PHOTO'] ?>" alt="photo de <?= $individu['NOM'] ?>"/>
					</div>

					<!-- Informations sur l'individu -->
					<div class="col-lg-5 offset-lg-0 col-md-8 offset-md-2 col-10 offset-1">
						<h1 id="nomIndividu"><?= $individu['NOM'] ?></h1>
						<table class="table">
							<tr>
								<th scope="row">Date de naissance</th>
								<td><?= $date_naissance ?></td>
							</tr>
							<tr>
								<th scope="row">Métiers</th>
								<td><?= $metiers ?></td>
							</tr>
							<tr>
								<th scope="row">Nationalité</th>
								<td><?= $individu['NATIONALITE'] ?></td>
							</tr>
							<tr>
								<th scope="row">Age</th>
								<td><?php if(!empty($date_naissance)) { echo $age . ' ans'; } ?></td>
							</tr>
							<tr>
								<th scope="row">Genre</th>
								<td><?= $genre ?></td>
							</tr>
						</table>
					</div>
				</div>

				<!-- Biographie -->
				<div class="row pt-3">
					<div class="col-md-8 offset-md-2 col-10 offset-1">
						<h1 class="champIndividu border-bottom">Biographie</h1>
						<div class="text-break"><?= nl2br($individu['BIOGRAPHIE']) ?></div>
					</div>
				</div>

				<!-- Filmographie -->
				<div class="row pt-3">
					<div class="col-md-8 offset-md-2 col-10 offset-1">
						<h1 class="champIndividu border-bottom">Filmographie</h1>
						<div class="row">
							<?php foreach($films as $film) { ?>
								<div class="col-lg-3 col-md-4 col-6">
									<a href="film?id=<?= $film['id_film'] ?>"><img class= "img-fluid" src="img/affiches/<?= $film['AFFICHE'] ?>" alt="affiche <?= $film['TITRE'] ?>">
									<?= $film['TITRE'] ?></a>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } else { ?>
				<h2 class="text-center">Erreur : Mauvais ID individu ou individu inexistant</h2>
			<?php } ?>
		</div>
    </body>
</html>