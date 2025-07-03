<?php
/**
 * View de la page home.php (/home)
 *
 * @version 4.0
 * @author Alain Barbier alias "Metroidzeta" (fullstack: backend/frontend)
 * @author Roger Huang (frontend/design)
 */
?>
		<main>
			<div class="container">

				<!-- Carousel -->
				<?php if (!empty($carouselItems)) : ?>
					<section class="owl-carousel owl-theme">
						<?php foreach ($carouselItems as $item): ?>
							<div class="slide">
								<div class="slide-content text-center">
									<a href="film?id=<?= $item['id_film'] ?>">
										<img class="img-fluid" loading="lazy" src="/CineToile/img/carousel/<?= htmlspecialchars($item['image']) ?>" alt="image carousel <?= htmlspecialchars($item['titre']) ?>"/>
										<p>
											<span class="carouselTitre"><?= htmlspecialchars($item['titre']) ?></span>
											<br/>
											<span class="legende"><?= htmlspecialchars($item['legend']) ?></span>
										</p>
									</a>
								</div>
							</div>
						<?php endforeach; ?>
					</section>
				<?php endif; ?>

				<!-- À l'affiche -->
				<?php if (!empty($films)): ?>
					<section id="a_laffiche">
						<h1 class="text-center border-bottom">À l'affiche</h1>
						<div class="row">
							<?php foreach($films as $film): ?>
								<div class="col-6 col-md-4 col-lg-3">
									<div class="affiche">
										<a href="film?id=<?= (int) $film['id_film'] ?>">
											<img class="img-fluid" loading="lazy" src="/CineToile/img/affiches/<?= htmlspecialchars($film['AFFICHE']) ?>" alt="affiche du film <?= htmlspecialchars($film['TITRE']) ?>"/>
											<div class="overlay">
												<div class="titreAffiche text-uppercase"><?= htmlspecialchars($film['TITRE']) ?></div>
												<div class="soustitreAffiche"><?= realisateurs_avec_phrase($film['realisateurs']) ?></div>
											</div>
										</a>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</section>
				<?php else: ?>
					<p class="text-center w-100">Aucun film à l'affiche actuellement.</p>
				<?php endif; ?>

				<!-- Actualités -->
				<?php if (!empty($articles)): ?>
					<section class="pt-2">
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
														<img class="card-img-top" loading="lazy" src="/CineToile/img/actualites/<?= htmlspecialchars($article['IMAGE_ARTICLE']) ?>" alt="image article <?= htmlspecialchars($article['TITRE']) ?>" />
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
					</section>
				<?php else: ?>
					<p class="text-center w-100">Aucun article pour le moment.</p>
				<?php endif; ?>

			</div>
		</main>