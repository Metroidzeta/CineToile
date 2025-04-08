<?php
/**
 * Page : Affichage de la page actualités (/actualites)
 * Version : v3.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/extraire_date.php';

define('NB_ARTICLES_PAR_PAGE', 8); // Nombre d'articles par page (par défaut : 8)
$valid = false;

if (!isset($_GET['page']) || ctype_digit($_GET['page'])) {
	$numPage = isset($_GET['page']) ? (int) $_GET['page'] : 1; // On récupère le numéro de la page (1 par défaut)

	$nbArticles = executeReqFetch( // Nombre d'articles existants dans la BDD
		$dbh,
		'SELECT COUNT(*) AS nb_articles
		FROM articles')['nb_articles'];

	$nbPages = (int) ceil($nbArticles / NB_ARTICLES_PAR_PAGE); // Arrondi au nombre supérieur

	if ($numPage > 0 && $numPage <= $nbPages) {
		$offset = ($numPage - 1) * NB_ARTICLES_PAR_PAGE;

		$articles = executeReqFetchAllArgsLimitOffset( // On récupère les articles correspondant à cette page
			$dbh,
			'SELECT * FROM articles
			ORDER BY DATE_ARTICLE DESC LIMIT :limit OFFSET :offset',
			[':limit' => NB_ARTICLES_PAR_PAGE,':offset' => $offset]
		);

		$startPage = max($numPage - 2, 1);
		$endPage = min($numPage + 2, $nbPages);
		$valid = true;
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

		<div class="container" style="min-height:100vh;">
			<?php if ($valid): ?>
				<div id="actu">
					<h1 class="text-center border-bottom">Actualités</h1>
					<div class="row">
						<?php foreach ($articles as $article): ?>
							<div class="col-12 col-md-6 col-lg-3">
								<a href="article?id=<?= (int) $article['id_article'] ?>">
									<div class="card w-300 h-200 mt-3">
										<img class="card-img-top" src="img/actualites/<?= basename(rawurldecode($article['IMAGE_ARTICLE'])) ?>" alt="image article <?= htmlspecialchars($article['TITRE']) ?>"/>
										<div class="card-body">
											<h5 class="card-title"><?= $article['TITRE'] ?></h5>
											<p class="card-text"><?= nl2br(htmlspecialchars($article['RESUME'])) ?></p>
											<span class="date_article"><?= obtenirDate($article['DATE_ARTICLE']) ?></span>
										</div>
									</div>
								</a>
							</div>
						<?php endforeach; ?>
					</div>

					<div class="row pt-3">
						<div class="col-3">
							<a href="home">« Retour</a>
						</div>
						<div class="col-3 offset-6">
							<?php if ($startPage !== 1): ?>
								<a href="actualites">« premier</a> ...
							<?php endif; ?>

							<?php for ($i = $startPage; $i <= $endPage; $i++): ?>
								<a href="actualites?page=<?= $i ?>" <?= $i === $numPage ? 'class="text-warning fw-bold"' : '' ?>> <?= $i ?></a>
							<?php endfor; ?>

							<?php if ($endPage !== $nbPages): ?>
								... <a href="actualites?page=<?= $nbPages ?>">dernier »</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php else: ?>
				<h2 class="text-center">Erreur : Mauvais numéro de page ou page inexistante</h2>
			<?php endif; ?>
		</div>

		<?php require $racine . '/CineToile/base/footer.php'; ?>
	</body>
</html>