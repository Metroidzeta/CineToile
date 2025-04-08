<?php
/**
 * Page : Affichage de la page d'un individu (/individu?id=X)
 * Version : v3.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/extraire_metiers.php';
require $racine . '/CineToile/util/extraire_date.php';

$individu = false;

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$id = (int) $_GET['id']; // On récupére l'id de l'individu venant de GET
	$individu = executeReqFetchArgs( // On récupére les données de cet individu
		$dbh,
		'SELECT * FROM individus
		WHERE id_individu = ?',
		[$id]
	);

	if ($individu) {
		$birth_date = obtenirDate($individu['DATE_NAISSANCE']);
		if (!empty($individu['DATE_NAISSANCE'])) {
			$time_birth_date = strtotime($individu['DATE_NAISSANCE']);
			$age = date('Y') - date('Y', $time_birth_date) - (date('md') < date('md', $time_birth_date) ? 1 : 0);
		}

		$genre = '';
		if (!empty($individu['GENRE'])) {
			$genre = $individu['GENRE'];
			$genre = $genre === 'H' ? 'Homme' : ($genre === 'F' ? 'Femme' : '');
		}

		$films = executeReqFetchAllArgs($dbh, // On récupére la filmographie associée à cet individu
			'SELECT DISTINCT films.id_film, TITRE, AFFICHE FROM films
			INNER JOIN films_individus ON films.id_film = films_individus.id_film
			WHERE id_individu = ?',
			[$id]
		);
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php require $racine . '/CineToile/base/head.php'; ?>
	</head>
	<body class="bg-dark">
		<?php require $racine . '/CineToile/base/barremenu.php'; ?>

		<div class="container bg-white" style="min-height:100vh;">
			<?php if ($individu): ?>
				<div class="row">
					<!-- Photo de l'individu -->
					<div class="text-center col-lg-3 col-md-8 offset-md-2 col-10 offset-1">
						<img class="img-fluid" src="/CineToile/img/individus/<?= basename(rawurldecode($individu['PHOTO'])) ?>" alt="photo de <?= htmlspecialchars($individu['NOM']) ?>"/>
					</div>

					<!-- Informations sur l'individu -->
					<div class="col-lg-5 offset-lg-0 col-md-8 offset-md-2 col-10 offset-1">
						<h1 id="nomIndividu"><?= htmlspecialchars($individu['NOM']) ?></h1>
						<table class="table">
							<tr>
								<th scope="row">Date de naissance</th>
								<td><?= $birth_date ?></td>
							</tr>
							<tr>
								<th scope="row">Métiers</th>
								<td><?= obtenirMetiers($individu['METIERS'], $individu['GENRE']) ?></td>
							</tr>
							<tr>
								<th scope="row">Nationalité</th>
								<td><?= htmlspecialchars($individu['NATIONALITE']) ?></td>
							</tr>
							<tr>
								<th scope="row">Age</th>
								<td><?= !empty($birth_date) ? $age . ' ans' : '' ?></td>
							</tr>
							<tr>
								<th scope="row">Genre</th>
								<td><?= $genre ?></td>
							</tr>
						</table>
					</div>
				</div>

				<!-- Biographie de l'individu -->
				<div class="row pt-3">
					<div class="col-md-8 offset-md-2 col-10 offset-1">
						<h1 class="champIndividu border-bottom">Biographie</h1>
						<div class="text-break"><?= nl2br(htmlspecialchars($individu['BIOGRAPHIE'])) ?></div>
					</div>
				</div>

				<!-- Filmographie de l'individu -->
				<div class="row pt-3">
					<div class="col-md-8 offset-md-2 col-10 offset-1">
						<h1 class="champIndividu border-bottom">Filmographie</h1>
						<div class="row">
							<?php foreach($films as $film): ?>
								<div class="col-lg-3 col-md-4 col-6">
									<a href="film?id=<?= (int) $film['id_film'] ?>">
										<img class= "img-fluid" src="/CineToile/img/affiches/<?= basename(rawurldecode($film['AFFICHE'])) ?>" alt="affiche <?= htmlspecialchars($film['TITRE']) ?>">
										<?= htmlspecialchars($film['TITRE']) ?>
									</a>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			<?php else: ?>
				<h2 class="text-center">Erreur : Mauvais ID individu ou individu inexistant</h2>
			<?php endif; ?>
		</div>
    </body>
</html>