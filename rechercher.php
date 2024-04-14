<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/afficher_realisateurs_avec_phrase.php';
require $racine . '/CineToile/util/extraire_metiers.php';

define('NB_RESULTATS_PAR_PAGE',8); // Le nombre de résultats par page, par défaut : 8
$nb_films_trouves = $nb_individus_trouves = 0;
$valid = false;

if(isset($_GET['q']) && !empty($_GET['q']) && !ctype_space($_GET['q'])) {
	$recherche = htmlspecialchars($_GET['q']);
	$recherche_save = $recherche;
	$recherche = mb_strtoupper($recherche); // mettre en majuscules
	$recherche = str_replace(' ','',$recherche); // supprimer les espaces
	if(!empty($recherche)) {
		if((isset($_GET['page']) && ctype_digit($_GET['page'])) || !isset($_GET['page'])) {
			$recherche_sql = "%{$recherche}%";
			$numPage = isset($_GET['page']) ? (int) $_GET['page'] : 1; // On récupére le numéro de la page

			$nbFilms_trouves = executeReqFetchArgs($dbh,'SELECT COUNT(*) AS nb_films FROM films WHERE UPPER(REPLACE(TITRE," ","")) LIKE ?',[$recherche_sql])['nb_films'];
			$nbIndividus_trouves = executeReqFetchArgs($dbh,'SELECT COUNT(*) AS nb_individus FROM individus WHERE UPPER(REPLACE(NOM," ","")) LIKE ?',[$recherche_sql])['nb_individus'];
			$nbPages = ceil(($nbFilms_trouves + $nbIndividus_trouves) / NB_RESULTATS_PAR_PAGE); // Arrondi au nombre supérieur

			if($nbFilms_trouves == 0 && $nbIndividus_trouves == 0 && $numPage == 1) {
				$debut = $fin = $nbPages = 1;
				$nbFilms_cette_page = $nbIndividus_cette_page = 0;
				$valid = true;
			} else if($numPage > 0 && $numPage <= $nbPages) {
				$offset_films = ($numPage - 1) * NB_RESULTATS_PAR_PAGE;
				$films = executeReqFetchAllArgsLimitOffsetFirstSTR($dbh,'SELECT id_film, TITRE, AFFICHE FROM films WHERE UPPER(REPLACE(TITRE," ","")) LIKE :recherche LIMIT :limit OFFSET :offset',[':recherche' => $recherche_sql,':limit' => NB_RESULTATS_PAR_PAGE,':offset' => $offset_films]);
				$nbFilms_cette_page = count($films);

				if($nbFilms_cette_page < NB_RESULTATS_PAR_PAGE) {
					$nbPages_ayant_films = ceil($nbFilms_trouves / NB_RESULTATS_PAR_PAGE); // Arrondi au nombre supérieur
					$offset_individus = $numPage - $nbPages_ayant_films == 0 ? 0 : ($numPage - $nbPages_ayant_films) * NB_RESULTATS_PAR_PAGE - ($nbFilms_trouves % NB_RESULTATS_PAR_PAGE);
					if($numPage - $nbPages_ayant_films > 0 && $nbFilms_trouves % NB_RESULTATS_PAR_PAGE == 0) {
						$offset_individus = ($numPage - 1 - $nbPages_ayant_films) * NB_RESULTATS_PAR_PAGE;
					}
					$limit_individus = NB_RESULTATS_PAR_PAGE - $nbFilms_cette_page;
					$individus = executeReqFetchAllArgsLimitOffsetFirstSTR($dbh,'SELECT id_individu, NOM, METIERS, GENRE, PHOTO FROM individus WHERE UPPER(REPLACE(NOM," ","")) LIKE :recherche LIMIT :limit OFFSET :offset',[':recherche' => $recherche_sql,':limit' => $limit_individus,':offset' => $offset_individus]);
					$nbIndividus_cette_page = count($individus);
				} else {
					$nbIndividus_cette_page = 0;
				}

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
		<div class="container bg-white" style="min-height: 100vh;">
			<?php if(!empty($recherche)) {
				if($valid) {
					if($nbFilms_cette_page + $nbIndividus_cette_page > 0) {
						if($nbFilms_cette_page > 0) { ?>
							<div class="text-center">
								<b>FILMS :</b> (<?php echo $nbFilms_trouves . ' résultat'; if($nbFilms_trouves > 1) { echo 's'; } echo ' trouvé'; if($nbFilms_trouves > 1) { echo 's'; } ?>)
							</div>
							<?php foreach($films as $film) {
								// On récupére le nom du (ou des) réalisateur(s) associé(s) à ce film
								$realisateurs = executeReqFetchAllArgs($dbh,'SELECT NOM FROM individus INNER JOIN films_individus ON individus.id_individu = films_individus.id_individu WHERE id_film = ? AND role = "R"',[$film['id_film']]); ?>
								<div class="row pt-4">
									<div class="caseRecherche col-10 offset-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
										<div class="row">
											<div class="col-6 px-0">
												<a href="film?id=<?= $film['id_film'] ?>">
													<img class="img-fluid" src="img/affiches/<?= $film['AFFICHE'] ?>" alt="affiche <?= $film['TITRE'] ?>"/>
												</a>
											</div>
											<div class="col-6 mt-auto mb-auto">
												<div class="text-uppercase">
													<a href="film?id=<?= $film['id_film'] ?>"><?= $film['TITRE'] ?></a>
												</div>
												<small class="text-muted"><i>
													<?php realisateurs_avec_phrase($realisateurs); ?>
												</i></small>
											</div>
										</div>
									</div>
								</div>
							<?php }
						}

						if($nbIndividus_cette_page > 0) { ?>
							<div class="<?php if($nbFilms_cette_page > 0) { echo 'pt-4'; } ?> text-center">
								<b>INDIVIDUS :</b> (<?php echo $nbIndividus_trouves . ' résultat'; if($nbIndividus_trouves > 1) { echo 's'; } echo ' trouvé'; if($nbIndividus_trouves > 1) { echo 's'; } ?>)
							</div>
							<?php foreach($individus as $individu) {
								$metiers = obtenirMetiers($individu['METIERS'],$individu['GENRE']); ?>
								<div class="row pt-4">
									<div class="caseRecherche col-10 offset-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
										<div class="row">
											<div class="col-6 px-0">
												<a href="individu?id=<?= $individu['id_individu'] ?>">
													<img class="img-fluid" src="img/individus/<?= $individu['PHOTO'] ?>" alt="photo <?= $individu['NOM'] ?>"/>
												</a>
											</div>
											<div class="col-6 mt-auto mb-auto">
												<div>
													<a href="individu?id=<?= $individu['id_individu'] ?>"><?= $individu['NOM'] ?></a>
												</div>
												<small class="text-muted"><i><?php if(!empty($metiers)) { echo $metiers; } ?></i></small>
											</div>
										</div>
									</div>
								</div>
							<?php }
						} ?>
						<div class="row pt-3">
							<div class="col-md-3 offset-md-3 col-3 offset-1">
								<a href="home">« Retour</a>
							</div>
							<div class="col-md-3 offset-md-2 col-3 offset-4">
								<?php if($debut != 1) { ?><a href="rechercher?q=<?= $recherche_save ?>&page=1">« premier </a> ...<?php }
								for($i = $debut; $i <= $fin; $i++) { ?>
									<a href="rechercher?q=<?= $recherche_save ?>&page=<?= $i ?>" <?php if($i == $numPage) { echo 'class="text-warning fw-bold"'; } ?>> <?= $i ?></a>
								<?php }
								if($fin != $nbPages) { ?> ... <a href="rechercher?q=<?= $recherche_save ?>&page=<?= $nbPages ?>"> dernier »</a><?php } ?>
							</div>
						</div>
					<?php } else { ?>
						<div class="row">
							<div class="col-12 text-center">Aucun résultat trouvé</div> 
						</div>
						<div class="row pt-4">
							<div class="col-12 text-center">
								<a href="home">« Retour</a>
							</div>
						</div>
					<?php }
				} else { ?>
					<h2 class="text-center">Erreur : Mauvais numéro de page ou page inexistante</h2>
				<?php }
			} else { ?>
				<h2 class="text-center">Erreur : La recherche est vide</h2>
			<?php } ?>
		</div>
    </body>
</html>