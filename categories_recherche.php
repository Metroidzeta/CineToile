<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/afficher_realisateurs_avec_phrase.php';

define('NB_FILMS_PAR_PAGE', 8); // Le nombre de films par page, par défaut : 8
$categorie = $valid = false;

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$id = (int) $_GET['id']; // On récupère l'id de la catégorie venant de GET
	$categorie = executeReqFetchArgs(  // On récupére le nom de cette catégorie
		$dbh,
		'SELECT NOM FROM categories
		WHERE id_categorie = ?',
		[$id]
	);

	if ($categorie) { // On vérifie que cette categorie existe bien
		if ((isset($_GET['page']) && ctype_digit($_GET['page'])) || !isset($_GET['page'])) {
			$numPage = isset($_GET['page']) ? (int) $_GET['page'] : 1; // On récupère le numéro de la page venant de GET (ou 1 par défaut)

			// On récupére le nombre de films existants associés à cette catégorie
			$nbFilms_trouves = executeReqFetchArgs(
				$dbh,
				'SELECT COUNT(*) AS nb_films FROM films
				INNER JOIN films_categories ON films.id_film = films_categories.id_film
				WHERE id_categorie = ?',
				[$id])['nb_films'];

			$nbPages = ceil($nbFilms_trouves / NB_FILMS_PAR_PAGE);

			if($nbFilms_trouves == 0 && $numPage == 1) {
				$startPage = $endPage = $nbPages = 1;
				$valid = true;
			} else if($numPage > 0 && $numPage <= $nbPages) {
				$offset_films = ($numPage - 1) * NB_FILMS_PAR_PAGE;
				$films = executeReqFetchAllArgsLimitOffset(
					$dbh,
					'SELECT films.id_film, TITRE, AFFICHE FROM films
					INNER JOIN films_categories ON films.id_film = films_categories.id_film
					WHERE id_categorie = :id LIMIT :limit OFFSET :offset',
					[':id' => $id,':limit' => NB_FILMS_PAR_PAGE,':offset' => $offset_films]
				);

				$startPage = max($numPage - 2, 1);
				$endPage = min($numPage + 2, $nbPages);
				$valid = true;
			}
		}
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
			<?php if ($categorie): ?>
				<?php if ($valid): ?>
					<div class="text-center">
						<b>FILMS :</b> (<?= $nbFilms_trouves ?> résultat<?= ($nbFilms_trouves > 1 ? 's' : '') ?> trouvé<?= ($nbFilms_trouves > 1 ? 's' : '')?> dans la catégorie "<?= $categorie['NOM'] ?>")
					</div>

					<?php if ($nbFilms_trouves > 0): ?>
						<?php foreach ($films as $film):
							$realisateurs = executeReqFetchAllArgs( // On récupére le(s) réalisateur(s) associé(s) à ce film
								$dbh,
								'SELECT individus.id_individu, NOM FROM individus INNER JOIN films_individus ON individus.id_individu = films_individus.id_individu
								WHERE id_film = ? AND role = "R"',
								[$film['id_film']]
							);
							?>
							<div class="row pt-4">
								<div class="caseRecherche col-10 offset-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
									<div class="row">
										<div class="col-6 px-0">
											<a href="film?id=<?= (int) $film['id_film'] ?>">
												<img class="img-fluid" src="img/affiches/<?= basename(rawurldecode($film['AFFICHE'])) ?>" alt="affiche <?= htmlspecialchars($film['TITRE']) ?>"/>
											</a>
										</div>
										<div class="col-6 mt-auto mb-auto">
											<div class="text-uppercase">
												<a href="film?id=<?= (int) $film['id_film'] ?>"><?= htmlspecialchars($film['TITRE']) ?></a>
											</div>
											<small class="text-muted">
												<i><?php realisateurs_avec_phrase($realisateurs); ?></i>
											</small>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else: ?>
						<div class="pt-4 text-center">Aucun film trouvé</div>
					<?php endif; ?>

					<div class="row pt-3">
						<div class="col-md-3 offset-md-3 col-3 offset-1">
							<a href="categories">« Retour</a>
						</div>
						<div class="col-md-3 offset-md-2 col-3 offset-4">
							<?php if ($startPage !== 1): ?>
								<a href="categories_recherche?id=<?= $id ?>">« premier</a> ...
							<?php endif; ?>

							<?php for ($i = $startPage; $i <= $endPage; $i++): ?>
								<a href="categories_recherche?id=<?= $id ?>&page=<?= $i ?>" <?= $i === $numPage ? 'class="text-warning fw-bold"' : '' ?>> <?= $i ?></a>
							<?php endfor; ?>

							<?php if($endPage !== $nbPages): ?>
								... <a href="categories_recherche?id=<?= $id ?>&page=<?= $nbPages ?>">dernier »</a>
							<?php endif; ?>
						</div>
					</div>
				<?php else: ?>
					<h2 class="text-center">Erreur : Mauvais numéro de page ou page inexistante</h2>
				<?php endif; ?>
			<?php else: ?>
				<h2 class="text-center">Erreur : Mauvaise ID categorie ou categorie inexistante</h2>
			<?php endif; ?>
		</div>

    </body>
</html>