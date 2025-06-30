<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/extraire_date.php';
require $racine . '/CineToile/util/afficher_notes_etoiles.php';

$film = $valid = false;
define('NB_AVIS_PAR_PAGE', 10); // Nombre d'articles par page (par défaut : 10)

if(isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$id = (int) $_GET['id'];
	$film = executeReqFetchArgs($dbh, // On récupère les données du film concerné
		'SELECT TITRE, AFFICHE FROM films
		WHERE id_film = ?',[$id]
	);

	if ($film) {
		if (!isset($_GET['page']) || ctype_digit($_GET['page'])) {
			$numPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

			$nbAvis = executeReqFetchArgs($dbh, // On récupére le nombre d'articles existant
				'SELECT COUNT(*) AS nb_avis FROM films_avis
				WHERE id_film = ?',[$id])['nb_avis'];

			$nbPages = (int) ceil($nbAvis / NB_AVIS_PAR_PAGE); // Arrondit au nombre supérieur

			if($nbAvis == 0 && $numPage == 1) {
				$startPage = $endPage = $nbPages = 1;
				$valid = true;
			} else if($numPage > 0 && $numPage <= $nbPages) {
				$offset_avis = ($numPage - 1) * NB_AVIS_PAR_PAGE;
				$les_avis = executeReqFetchAllArgsLimitOffset($dbh,
					'SELECT u.pseudo, fa.NOTE, fa.DATE_AVIS, fa.CRITIQUE FROM films_avis fa
					INNER JOIN utilisateurs u ON fa.pseudo = u.pseudo
					WHERE fa.id_film = :id ORDER BY DATE_AVIS DESC LIMIT :limit OFFSET :offset',
					[
						':id' => $id,
						':limit' => NB_AVIS_PAR_PAGE,
						':offset' => $offset_avis
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
			<?php if($film): ?>
				<?php if($valid): ?>
					<div class="row">
						<div class="col-md-1 offset-md-3 col-2 offset-1">
							<img class="img-fluid" src="img/affiches/<?= $film['AFFICHE'] ?>" alt="affiche <?= $film['TITRE'] ?>"/>
						</div>
						<div class="col-md-5 col-8">
							<h2>Avis pour le film : "<?= $film['TITRE'] ?>"</h2>
						</div>
					</div>
					<?php if($nbAvis > 0): ?>
						<div class="row pt-4">
							<div class="col-md-6 offset-md-3 col-10 offset-1">
								<div class="border-bottom"></div>
								<?php $nbAvis_extrait = count($les_avis);
								foreach ($les_avis as $index => $avis): ?>
									<?= getStars($avis['NOTE'], 18) . $avis['NOTE'] . ' publié le ' . extraireDate($avis['DATE_AVIS']) . ' par ' . $avis['pseudo'] ?>
									<br/><div class="text-break">
										<?= $avis['CRITIQUE'] ? nl2br(htmlspecialchars($avis['CRITIQUE'])) : '' ?>
									</div><br/>
									<?php if($index + 1 < $nbAvis_extrait): ?>
										<div class="border-bottom"></div>
									<?php endif;
								endforeach; ?>
							</div>
						</div>
					<?php else: ?>
						<h5 class="text-center">Ce film n'a pas encore d'avis.</h5>
					<?php endif; ?>
					<div class="row pt-3">
						<div class="col-md-3 offset-md-3 col-3 offset-1">
							<a href="film?id=<?= $id ?>">« Retour</a>
						</div>

						<div class="col-md-3 offset-md-2 col-3 offset-4">
							<?php if($startPage != 1): ?>
								<a href="film_voir_avis?id=<?= $id ?>">« premier</a> ...
							<?php endif; ?>

							<?php for($i = $startPage; $i <= $endPage; $i++): ?>
								<a href="film_voir_avis?id=<?= $id ?>&page=<?= $i ?>" <?= $i === $numPage ? 'class="text-warning fw-bold"' : '' ?>> <?= $i ?></a>
							<?php endfor; ?>

							<?php if($endPage < $nbPages): ?>
								... <a href="film_voir_avis?id=<?= $id ?>&page=<?= $nbPages ?>">dernier »</a>
							<?php endif; ?>
						</div>
					</div>
				<?php else: ?>
					<h2 class="text-center">Erreur : Mauvais numéro de page ou page inexistante</h2>
				<?php endif; ?>
			<?php else: ?>
				<h2 class="text-center">Erreur : Mauvais ID de film ou film inexistant</h2>
			<?php endif; ?>
		</div>

	</body>
</html>