<?php
/**
 * Page : Affichage de la page catégories (/categories)
 * Version : v3.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';

$categories = executeReqFetchAll($dbh, 'SELECT * FROM categories'); // On récupére toutes les catégories
$nbCategories = count($categories);
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php require $racine . '/CineToile/base/head.php'; ?>
	</head>
	<body>
		<?php require $racine . '/CineToile/base/barremenu.php'; ?>

		<div class="container text-center">
			<h1 id="categories">Catégories <small class="text-muted">(<?= $nbCategories ?> résultat<?= ($nbCategories > 1 ? 's' : '') ?>)</small></h1>
			<div class="row pt-2">
				<?php if ($nbCategories > 0):
					foreach ($categories as $index => $categorie): ?>
						<div class="boxCategories col-2 offset-<?= $index % 3 === 0 ? '2' : '1' ?> mt-3 p-2">
							<a class="lienCategories" href="categories_recherche?id=<?= (int) $categorie['id_categorie'] ?>"><?= htmlspecialchars($categorie['NOM']) ?></a>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="col-12">Aucune catégorie trouvée</div>
				<?php endif; ?>
			</div>
		</div>

		<?php require $racine . '/CineToile/base/footer.php'; ?>
	</body>
</html>