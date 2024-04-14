<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/extraire_date.php';

define('NB_ARTICLES_PAR_PAGE',8); // Le nombre d'articles par page, par défaut : 8
$valid = false;

if((isset($_GET['page']) && ctype_digit($_GET['page'])) || !isset($_GET['page'])) {
	$numPage = isset($_GET['page']) ? (int) $_GET['page'] : 1; // On récupère le numéro de la page (ou 1 par défaut)

	$nbArticles = executeReqFetch($dbh,'SELECT COUNT(*) AS nb_articles FROM articles')['nb_articles']; // On récupére le nombre d'articles existants
	$nbPages = ceil($nbArticles / NB_ARTICLES_PAR_PAGE); // Arrondi au nombre supérieur

	if($numPage > 0 && $numPage <= $nbPages) {
		$offset_article = ($numPage - 1) * NB_ARTICLES_PAR_PAGE;
		// On récupére les articles correspondant à cette page
		$articles = executeReqFetchAllArgsLimitOffset($dbh,'SELECT * FROM articles ORDER BY DATE_ARTICLE DESC LIMIT :limit OFFSET :offset',[':limit' => NB_ARTICLES_PAR_PAGE,':offset' => $offset_article]);

		$debut = max($numPage - 2,1);
		$fin = min($numPage + 2,$nbPages);
		$valid = true;
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head><?php require $racine . '/CineToile/base/head.php'; ?></head>
	<body><?php require $racine . '/CineToile/base/barremenu.php'; ?>

		<div class="container" style="min-height:100vh;">
			<?php if($valid) { ?>
				<div id="actu">
					<h1 class="text-center border-bottom">Actualités</h1>
					<div class="row">
						<?php foreach($articles as $article) {
							$date_article = obtenirDate($article['DATE_ARTICLE']); ?>
							<div class="col-12 col-md-6 col-lg-3">
								<a href="article?id=<?= $article['id_article'] ?>">
									<div class="card w-300 h-200 mt-3">
										<img class="card-img-top" src="img/actualites/<?= $article['IMAGE_ARTICLE'] ?>" alt="image article <?= $article['id_article'] ?>"/>
										<div class="card-body">
											<h5 class="card-title"><?= $article['TITRE'] ?></h5>
											<p class="card-text"><?= nl2br($article['RESUME']) ?></p>
											<span class="date_article"><?= $date_article ?></span>
										</div>
									</div>
								</a>
							</div>
						<?php } ?>
					</div>
					<div class="row pt-3">
						<div class="col-3">
							<a href="home">« Retour</a>
						</div>
						<div class="col-3 offset-6">
							<?php if($debut != 1) { ?><a href="actualites?page=1">« premier </a> ...<?php }
							for($i = $debut; $i <= $fin; $i++) { ?>
								<a href="actualites?page=<?= $i ?>" <?php if($i == $numPage) { echo 'class="text-warning fw-bold"'; } ?>> <?= $i ?></a>
							<?php }
							if($fin != $nbPages) { ?> ... <a href="actualites?page=<?= $nbPages ?>"> dernier »</a><?php } ?>
						</div>
					</div>
				</div>
			<?php } else { ?>
				<h2 class="text-center">Erreur : Mauvais numéro de page ou page inexistante</h2>
			<?php } ?>
		</div>
	<?php require $racine . '/CineToile/base/footer.php'; ?>
	</body>
</html>