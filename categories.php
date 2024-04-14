<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';

$categories = executeReqFetchAll($dbh,'SELECT * FROM categories'); // On récupére toutes les catégories de films
$nbCategories = count($categories);
?>
<!DOCTYPE html>
<html lang="fr">
	<head><?php require $racine . '/CineToile/base/head.php'; ?></head>
	<body><?php require $racine . '/CineToile/base/barremenu.php'; ?>
		<div class="container text-center">
			<h1 id="categories">Catégories <small class="text-muted">(<?php echo $nbCategories . ' résultat'; if($nbCategories > 1) { echo 's'; } ?>)</small></h1>
			<div class="row pt-2">
				<?php if($nbCategories > 0) {
					$compteur = 0;
					foreach($categories as $categorie) { ?>
						<div class="boxCategories col-2 offset-<?php if($compteur % 3 == 0) { echo '2'; } else { echo '1'; } ?> mt-3 p-2">
							<a class="lienCategories" href="categories_recherche?id=<?= $categorie['id_categorie'] ?>"><?= $categorie['NOM'] ?></a>
						</div>
						<?php $compteur++;
					}
				} else { ?>
					<div class="col-12">Aucune catégorie trouvée</div>
				<?php } ?>
			</div>
		</div>
	<?php require $racine . '/CineToile/base/footer.php'; ?>
	</body>
</html>