<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/extraire_date.php';

define('NB_ARTICLES_PAR_PAGE',8); // Le nombre d'articles par page (par défaut : 8)
$article = false;

if(isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$id = (int) $_GET['id']; // On récupére l'id de l'article
	$article = executeReqFetchArgs($dbh,'SELECT * FROM articles WHERE id_article = ?',[$id]); // On récupére les données de l'article

	if($article) {
		$date_article = obtenirDate($article['DATE_ARTICLE']);

		$nbArticles = executeReqFetch($dbh,'SELECT COUNT(*) AS nb_articles FROM articles')['nb_articles']; // On récupére le nb d'articles existant
		$nbPages = ceil($nbArticles / NB_ARTICLES_PAR_PAGE); // Arrondi au nombre supérieur
		$numPage_precedente = $nbPages - intdiv($id - 1,NB_ARTICLES_PAR_PAGE);
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head><?php require $racine . '/CineToile/base/head.php'; ?></head>
	<body><?php require $racine . '/CineToile/base/barremenu.php'; ?>
		<div class="container" style="min-height:100vh;">
			<?php if($article) { ?>
				<!-- Date -->
				<div class="dateArticle text-center"><?= $date_article ?></div>

				<!-- Titre -->
				<div class="row pt-2">
					<div class="col-8 offset-2">
						<div class="titreArticle pb-1 border-bottom text-center"><?= $article['TITRE'] ?></div>
					</div>
				</div>

				<!-- Résumé -->
				<div class="row pt-3">
					<div class="col-8 offset-2 text-center"><?= nl2br($article['RESUME']) ?></div>
				</div>

				<!-- Image de l'article -->
				<div class="row pt-3">
					<div class="col-10 offset-1 text-center">
						<img class="img-fluid" src="img/actualites/<?= $article['IMAGE_ARTICLE'] ?>" alt="image article <?= $id ?>"/>
					</div>
				</div>

				<!-- Contenu -->
				<div class="row pt-3">
					<div class="col-10 offset-1" style="text-align:justify;">
						<p><?= nl2br($article['CONTENU']) ?></p>
					</div>
				</div>

				<!-- Lien Retour -->
				<div class="pt-4 text-center">
					<a href="actualites?page=<?= $numPage_precedente ?>">« Retour</a>
				</div>
			<?php } else { ?>
				<h2 class="text-center">Erreur : Mauvais ID article ou article inexistant</h2>
			<?php } ?>
		</div>
		<?php require $racine . '/CineToile/base/footer.php'; ?>
	</body>
</html>