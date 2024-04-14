<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/extraire_date.php';
require $racine . '/CineToile/util/afficher_realisateurs_avec_phrase.php';

$films_carousel = executeReqFetchAll($dbh,'SELECT id_film, TITRE FROM films WHERE id_film IN (1,2,4)'); // ordre croissant
$ordre_passage = [2,0,1]; // indices du tableau des données de films du carousel
$tab_images_carousel = ['interstellar.jpeg','oss_117_alerte_rouge.jpg','mourir_peut_attendre.jpg'];
$tab_legendes_carousel = [
	'Le film culte de Christopher Nolan',
	'Les nouvelles aventures d\'Hubert Bonisseur de La Bath',
	'James Bond plus classe que jamais'
];

$films_affiches = executeReqFetchAll($dbh,'SELECT id_film, TITRE, AFFICHE FROM films LIMIT 12');
$articles = executeReqFetchAll($dbh,'SELECT * FROM articles ORDER BY DATE_ARTICLE DESC LIMIT 6');
$nbArticles_sur_cette_page = count($articles);
?>
<!DOCTYPE html>
<html lang="fr">
	<head><?php require $racine . '/CineToile/base/head.php'; ?>
		<!-- Owl Carousel -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css"/>
	</head>
	<body><?php require $racine . '/CineToile/base/barremenu.php'; ?>
		<div class="container">
			<div class="owl-carousel owl-theme">
				<?php $count_films_carousel = count($films_carousel);
				$count_ordre_passage = count($ordre_passage);
				for($i = 0; $i < 3; $i++) {
					if($i < $count_films_carousel && $i < $count_ordre_passage) {
						$film = $films_carousel[$ordre_passage[$i]]; ?>
						<div class="slide">
							<div class="slide-content text-center">
								<a href="film?id=<?= $film['id_film'] ?>">
									<img class="img-fluid" src="img/carousel/<?= $tab_images_carousel[$i] ?>" alt="<?= $film['TITRE'] ?>"/>
									<p>
										<span class="carouselTitre"><?= $film['TITRE'] ?></span>
										<br />
										<span class="legende"><?= $tab_legendes_carousel[$i] ?></span>
									</p>
								</a>
							</div>
						</div>
					<?php }
				} ?>
			</div>

			<div id="a_laffiche">
				<h1 class="text-center border-bottom">A l'affiche</h1>
				<div class="row">
					<?php foreach($films_affiches as $film) {
						// On récupére le nom du (ou des) Réalisateur(s) associé(s) à ce film
						$realisateurs = executeReqFetchAllArgs($dbh,'SELECT NOM FROM individus INNER JOIN films_individus ON individus.id_individu = films_individus.id_individu WHERE id_film = ? AND role = "R"',[$film['id_film']]); ?>
						<div class="col-6 col-md-4 col-lg-3">
							<div class="affiche">
								<a href="film?id=<?= $film['id_film'] ?>">
									<img class="img-fluid" src="img/affiches/<?= $film['AFFICHE'] ?>" alt="affiche <?= $film['TITRE'] ?>"/>
									<div class="overlay">
										<div class="titreAffiche text-uppercase"><?= $film['TITRE'] ?></div>
										<div class="soustitreAffiche"><?php realisateurs_avec_phrase($realisateurs); ?></div>
									</div>
								</a>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>

			<div class="pt-2">
				<h1 class="text-center border-bottom">Actualités</h1>
				<div class="row">
					<?php $i = 1;
					foreach($articles as $article) {
						$date_article = obtenirDate($article['DATE_ARTICLE']);
						if($i == 3) { ?>
							<div id="actu_secondaire">
								<div class="row pt-3">
						<?php } ?>
									<div class="<?php if($i < 3) { echo 'col-12 col-md-6'; } else { echo 'col-6 col-lg-3'; } ?>">
										<a href="article?id=<?= $article['id_article'] ?>">
											<div class="card mt-3">
												<img class="card-img-top" src="img/actualites/<?= $article['IMAGE_ARTICLE'] ?>" alt="image article <?= $article['id_article'] ?>" />
												<div class="card-body">
													<h5 class="card-title"><?= $article['TITRE'] ?></h5>
													<p class="card-text"><?= nl2br($article['RESUME']) ?></p>
													<span class="date_article"><?= $date_article ?></span>
												</div>
											</div>
										</a>
									</div>
						<?php if($i == $nbArticles_sur_cette_page) { // Si c'est le dernier article ?>
								</div>
							</div>
						<?php }
						$i++;
					} ?>
				</div>
				<div class="pt-3">
					<a href="actualites">Voir toute l'actualité »</a>
				</div>
			</div>

		</div>
	<?php require $racine . '/CineToile/base/footer.php'; ?>
	</body>
</html>