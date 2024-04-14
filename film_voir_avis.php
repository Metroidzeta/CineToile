<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/extraire_date.php';
require $racine . '/CineToile/util/afficher_notes_etoiles.php';

$film = $valid = false;

if(isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$id = (int) $_GET['id']; // On récupère l'id du film venant de GET
	$film = executeReqFetchArgs($dbh,'SELECT TITRE, AFFICHE FROM films WHERE id_film = ?',[$id]); // Requête SQL pour vérifier que le film existe bien

	if($film) {
		if((isset($_GET['page']) && ctype_digit($_GET['page'])) || !isset($_GET['page'])) {
			$numPage = isset($_GET['page']) ? (int) $_GET['page'] : 1; // On récupère le numéro de la page venant de GET
			$nbAvis_par_page = 2; // nombre > 0

			// On récupére le nombre d'articles existant dans la BDD
			$nbAvis = executeReqFetchArgs($dbh,'SELECT COUNT(*) AS nb_avis FROM films_avis WHERE id_film = ?',[$id])['nb_avis'];
			$nbPages = ceil($nbAvis / $nbAvis_par_page); // Arrondit au nombre supérieur

			if($nbAvis == 0 && $numPage == 1) {
				$debut = $fin = $nbPages = 1;
				$valid = true;
			} else if($numPage > 0 && $numPage <= $nbPages) {
				$offset_avis = ($numPage - 1) * $nbAvis_par_page;
				$les_avis = executeReqFetchAllArgsLimitOffset($dbh,'SELECT utilisateurs.pseudo, NOTE, DATE_AVIS, CRITIQUE FROM films_avis INNER JOIN utilisateurs ON films_avis.pseudo = utilisateurs.pseudo WHERE id_film = :id ORDER BY DATE_AVIS DESC LIMIT :limit OFFSET :offset',[':id' => $id,':limit' => $nbAvis_par_page,':offset' => $offset_avis]);

				$debut = max($numPage - 2,1);
				$fin = min($numPage + 2,$nbPages);	
				$valid = true;
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head><?php require $racine . '/CineToile/base/head.php'; ?></head>
	<body class="bg-dark"><?php require $racine . '/CineToile/base/barremenu.php'; ?>
		<div class="container bg-white" style="min-height:100vh;">
			<?php if($film) {
				if($valid) { ?>
					<div class="row">
						<div class="col-md-1 offset-md-3 col-2 offset-1">
							<img class="img-fluid" src="img/affiches/<?= $film['AFFICHE'] ?>" alt="affiche <?= $film['TITRE'] ?>"/>
						</div>
						<div class="col-md-5 col-8">
							<h2>Avis pour le film : "<?= $film['TITRE'] ?>"</h2>
						</div>
					</div>
					<?php if($nbAvis > 0) { ?>
						<div class="row pt-4">
							<div class="col-md-6 offset-md-3 col-10 offset-1">
								<div class="border-bottom"></div>
								<?php $j = 1;
								$nbAvis_extrait = count($les_avis);
								foreach($les_avis as $avis) {
									$date_avis = obtenirDate($avis['DATE_AVIS']);
									echo obtenir_etoiles($avis['NOTE'],18);
									echo $avis['NOTE'] . ' publié le ' . $date_avis . ' par ' . $avis['pseudo']; ?>
									<br />
									<div class="text-break"><?= nl2br($avis['CRITIQUE']) ?></div>
									<br />
									<?php if($j < $nbAvis_extrait) { ?>
										<div class="border-bottom"></div>
									<?php }
									$j++;
								} ?>
							</div>
						</div>
					<?php } else { ?>
						<h5 class="text-center">Ce film n'a pas encore d'avis.</h5>
					<?php } ?>
					<div class="row pt-3">
						<div class="col-md-3 offset-md-3 col-3 offset-1">
							<a href="film?id=<?= $id ?>">« Retour</a>
						</div>
						<div class="col-md-3 offset-md-2 col-3 offset-4">
							<?php if($debut != 1) { ?><a href="film_voir_avis?id=<?= $id ?>&page=1">« premier </a> ...<?php }
							for($i = $debut; $i <= $fin; $i++) { ?>
								<a href="film_voir_avis?id=<?= $id ?>&page=<?= $i ?>" <?php if($i == $numPage) { echo 'class="text-warning fw-bold"'; } ?>> <?= $i ?></a>
							<?php }
							if($fin != $nbPages) { ?> ... <a href="film_voir_avis?id=<?= $id ?>&page=<?= $nbPages ?>"> dernier »</a><?php } ?>
						</div>
					</div>
				<?php } else { ?>
					<h2 class="text-center">Erreur : Mauvais numéro de page ou page inexistante</h2>
				<?php }
			} else { ?>
				<h2 class="text-center">Erreur : Mauvais ID de film ou film inexistant</h2>
			<?php } ?>
		</div>
	</body>
</html>