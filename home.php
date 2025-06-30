<?php
/**
 * Page : Affichage de la page home (/home)
 * Version : v4.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/extraire_date.php';
require $racine . '/CineToile/util/afficher_realisateurs_avec_phrase.php';

$carouselItems = [ // Les films du carousel
	[
		'id_film' => 4,
		'titre' => 'Interstellar',
		'image' => 'interstellar.jpeg',
		'legend' => 'Le film culte de Christopher Nolan'
	],
	[
		'id_film' => 1,
		'titre' => 'OSS 117 : Alerte rouge en Afrique noire',
		'image' => 'oss_117_alerte_rouge.jpg',
		'legend' => 'Les nouvelles aventures d\'Hubert Bonisseur de La Bath'
	],
	[
		'id_film' => 2,
		'titre' => '007 : Mourir peut attendre',
		'image' => 'mourir_peut_attendre.jpg',
		'legend' => 'James Bond plus classe que jamais'
	]
];

$films = executeReqFetchAll($dbh, // On prend les 12 premiers films de la BDD
	'SELECT f.id_film, f.TITRE, f.AFFICHE, GROUP_CONCAT(i.NOM ORDER BY i.NOM SEPARATOR ", ") AS realisateurs
	FROM films f
	LEFT JOIN films_individus fi ON f.id_film = fi.id_film AND fi.role = "R"
	LEFT JOIN individus i ON fi.id_individu = i.id_individu
	GROUP BY f.id_film
	LIMIT 12'
);

$articles = executeReqFetchAll($dbh, // On prend les 6 derniers articles de la BDD
	'SELECT *
	FROM articles
	ORDER BY DATE_ARTICLE DESC LIMIT 6'
);

$nbArticles = count($articles);
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php require $racine . '/CineToile/base/head.php'; ?>
		<!-- Owl Carousel -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css"/>
	</head>
	<body>
		<?php require $racine . '/CineToile/base/barremenu.php'; ?>

		<div class="container">
			<div class="owl-carousel owl-theme">
				<?php foreach ($carouselItems as $item): ?>
					<div class="slide">
						<div class="slide-content text-center">
							<a href="film?id=<?= $item['id_film'] ?>">
								<img class="img-fluid" loading="lazy" src="/CineToile/img/carousel/<?= $item['image'] ?>" alt="image carousel <?= $item['titre'] ?>"/>
								<p>
									<span class="carouselTitre"><?= htmlspecialchars($item['titre']) ?></span>
									<br/>
									<span class="legende"><?=  htmlspecialchars($item['legend']) ?></span>
								</p>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<div id="a_laffiche">
				<h1 class="text-center border-bottom">À l'affiche</h1>
				<div class="row">
					<?php foreach($films as $film): ?>
						<div class="col-6 col-md-4 col-lg-3">
							<div class="affiche">
								<a href="film?id=<?= (int) $film['id_film'] ?>">
									<img class="img-fluid" loading="lazy" src="/CineToile/img/affiches/<?= $film['AFFICHE'] ?>" alt="affiche du film <?= htmlspecialchars($film['TITRE']) ?>"/>
									<div class="overlay">
										<div class="titreAffiche text-uppercase"><?= htmlspecialchars($film['TITRE']) ?></div>
										<div class="soustitreAffiche"><?= realisateurs_avec_phrase($film['realisateurs']) ?></div>
									</div>
								</a>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="pt-2">
				<h1 class="text-center border-bottom">Actualités</h1>
				<div class="row">
					<?php foreach ($articles as $index => $article): ?>
						<?php if ($index === 2) : ?>
							<div id="actu_secondaire">
								<div class="row pt-3">
						<?php endif; ?>
									<div class="<?= ($index < 2) ? 'col-12 col-md-6' : 'col-6 col-lg-3' ?>">
										<a href="article?id=<?= (int) $article['id_article'] ?>">
											<div class="card mt-3">
												<img class="card-img-top" loading="lazy" src="/CineToile/img/actualites/<?= $article['IMAGE_ARTICLE'] ?>" alt="image article <?= htmlspecialchars($article['TITRE']) ?>" />
												<div class="card-body">
													<h5 class="card-title"><?= htmlspecialchars($article['TITRE']) ?></h5>
													<p class="card-text"><?= nl2br(htmlspecialchars($article['RESUME'])) ?></p>
													<span class="date_article"><?= extraireDate($article['DATE_ARTICLE']) ?></span>
												</div>
											</div>
										</a>
									</div>
						<?php if ($index + 1 === $nbArticles): // Si c'est le dernier article ?>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>

				<div class="pt-3">
					<a href="actualites">Voir toute l'actualité »</a>
				</div>
			</div>
		</div>

		<?php require $racine . '/CineToile/base/footer.php'; ?>
	</body>
</html>