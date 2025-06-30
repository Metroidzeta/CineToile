<?php
/**
 * Page : Affichage de la page de la recherche d'une catégorie (/categories_recherche?id=X&page=Y)
 * Version : v3.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/afficher_realisateurs_avec_phrase.php';

define('NB_FILMS_PAR_PAGE', 8); // Le nombre de films par page (par défaut : 8)
$categorie = $valid = false;

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$id = (int) $_GET['id'];
	$categorie = executeReqFetchArgs($dbh, // On récupére le nom de cette catégorie
		'SELECT NOM
		FROM categories
		WHERE id_categorie = ?',[$id]
	);

	if ($categorie) { // On vérifie que cette categorie existe bien
		if ((isset($_GET['page']) && ctype_digit($_GET['page'])) || !isset($_GET['page'])) {
			$numPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

			$nbFilms_trouves = executeReqFetchArgs($dbh, // Nombre de films existants dans cette catégorie
				'SELECT COUNT(*) AS nb_films FROM films f
				INNER JOIN films_categories fc ON f.id_film = fc.id_film
				WHERE fc.id_categorie = ?',[$id])['nb_films'];

			$nbPages = (int) ceil($nbFilms_trouves / NB_FILMS_PAR_PAGE); // Arrondie à l'entier supérieur

			if ($nbFilms_trouves == 0 && $numPage == 1) {
				$startPage = $endPage = $nbPages = 1;
				$valid = true;
			} else if ($numPage > 0 && $numPage <= $nbPages) {
				$offset_films = ($numPage - 1) * NB_FILMS_PAR_PAGE;
				$films = executeReqFetchAllArgsLimitOffset($dbh, // On récupère les films de cette catégorie correspondant à cette page
					'SELECT f.id_film, f.TITRE, f.AFFICHE FROM films f
					INNER JOIN films_categories fc ON f.id_film = fc.id_film
					WHERE fc.id_categorie = :id LIMIT :limit OFFSET :offset',
					[
						':id' => $id,':limit' => NB_FILMS_PAR_PAGE,
						':offset' => $offset_films
					]
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
						<b>FILMS :</b> (<?= $nbFilms_trouves ?> résultat<?= ($nbFilms_trouves > 1) ? 's' : '' ?> trouvé<?= ($nbFilms_trouves > 1) ? 's' : '' ?> dans la catégorie "<?= htmlspecialchars($categorie['NOM']) ?>")
					</div>

					<?php if ($nbFilms_trouves > 0): ?>
						<?php foreach ($films as $film):
							$realisateurs = executeReqFetchAllArgs($dbh, // On récupére le(s) réalisateur(s) associé(s) à ce film
								'SELECT i.id_individu, i.NOM FROM individus i
								INNER JOIN films_individus fi ON i.id_individu = fi.id_individu
								WHERE fi.id_film = ? AND fi.role = "R"',[$film['id_film']]
							);
						?>
							<div class="row pt-4">
								<div class="caseRecherche col-10 offset-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
									<div class="row">
										<div class="col-6 px-0">
											<a href="film?id=<?= (int) $film['id_film'] ?>">
												<img class="img-fluid" src="/CineToile/img/affiches/<?= basename(rawurldecode($film['AFFICHE'])) ?>" alt="affiche <?= htmlspecialchars($film['TITRE']) ?>"/>
											</a>
										</div>
										<div class="col-6 mt-auto mb-auto">
											<div class="text-uppercase">
												<a href="film?id=<?= (int) $film['id_film'] ?>"><?= htmlspecialchars($film['TITRE']) ?></a>
											</div>
											<small class="text-muted">
												<i><?= realisateurs_avec_phrase($realisateurs) ?></i>
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
								<?php if ($i === $numPage): ?>
									<span class="text-warning fw-bold"><?= $i ?></span>
								<?php else: ?>
									<a href="categories_recherche?id=<?= $id ?>&page=<?= $i ?>"> <?= $i ?></a>
								<?php endif; ?>
							<?php endfor; ?>

							<?php if($endPage < $nbPages): ?>
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