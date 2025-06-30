<?php 
/**
 * Page : Affichage de la page d'un article (/article?id=X)
 * Version : v3.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/extraire_date.php';

define('NB_ARTICLES_PAR_PAGE', 8); // Nombre d'articles par page (par défaut : 8)
$article = null;

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$id = (int) $_GET['id'];
	$article = executeReqFetchArgs($dbh, // On récupére les données de l'article
		'SELECT * FROM articles
		WHERE id_article = ?',[$id]
	);

	if($article) {
		$nbArticles = executeReqFetchColumn($dbh, // Nombre d'articles existants
			'SELECT COUNT(*) FROM articles'
		);
		$nbPages = (int) ceil($nbArticles / NB_ARTICLES_PAR_PAGE); // Arrondie à l'entier supérieur
		$numPage_precedente = $nbPages - intdiv($id - 1, NB_ARTICLES_PAR_PAGE);
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
			<?php if ($article): ?>
				<!-- Date -->
				<div class="dateArticle text-center">
					<?= extraireDate($article['DATE_ARTICLE']) ?>
				</div>

				<!-- Titre -->
				<div class="row pt-2">
					<div class="col-8 offset-2">
						<div class="titreArticle pb-1 border-bottom text-center">
							<?= htmlspecialchars($article['TITRE']) ?>
						</div>
					</div>
				</div>

				<!-- Résumé -->
				<div class="row pt-3">
					<div class="col-8 offset-2 text-center">
						<?= nl2br(htmlspecialchars($article['RESUME'])) ?>
					</div>
				</div>

				<!-- Image  -->
				<div class="row pt-3">
					<div class="col-10 offset-1 text-center">
						<img class="img-fluid" loading="lazy" src="/CineToile/img/actualites/<?= basename(rawurldecode($article['IMAGE_ARTICLE'])) ?>" alt="image article <?= htmlspecialchars($article['TITRE']) ?>"/>
					</div>
				</div>

				<!-- Contenu -->
				<div class="row pt-3">
					<div class="col-10 offset-1" style="text-align:justify;">
						<p><?= nl2br(htmlspecialchars($article['CONTENU'])) ?></p>
					</div>
				</div>

				<!-- Lien de Retour -->
				<div class="pt-4 text-center">
					<a href="actualites?page=<?= (int) $numPage_precedente ?>">« Retour</a>
				</div>
			<?php else: ?>
				<h2 class="text-center">Erreur : Mauvais ID article ou article inexistant</h2>
			<?php endif; ?>
		</div>

		<?php require $racine . '/CineToile/base/footer.php'; ?>
	</body>
</html>