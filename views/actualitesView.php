<?php
/**
 * View de la page actualites.php (/actualites?page=X)
 *
 * @version 4.0
 * @author Alain Barbier alias "Metroidzeta" (fullstack: backend/frontend)
 * @author Roger Huang (frontend/design)
 */
?>
		<main>
			<div class="container" style="min-height:100vh;">
				<?php if ($valid): ?>
					<?php if ($nbArticles === 0): ?>
						<h2 class="text-center pt-5">Aucun article disponible pour le moment.</h2>
						<div class="text-center pt-3">
							<a href="home">« Retour à l'accueil</a>
						</div>
					<?php else: ?>
						<div id="actu">
							<!-- Les articles -->
							<h1 class="text-center border-bottom">Actualités</h1>
							<div class="row">
								<?php foreach ($articles as $article): ?>
									<div class="col-12 col-md-6 col-lg-3">
										<a href="article?id=<?= (int) $article['id_article'] ?>">
											<div class="card w-300 h-200 mt-3">
												<img class="card-img-top" loading="lazy" src="/CineToile/img/actualites/<?= htmlspecialchars(basename(rawurldecode($article['IMAGE_ARTICLE']))) ?>" alt="image article <?= htmlspecialchars($article['TITRE']) ?>"/>
												<div class="card-body">
													<h5 class="card-title"><?= htmlspecialchars($article['TITRE']) ?></h5>
													<p class="card-text"><?= nl2br(htmlspecialchars($article['RESUME'])) ?></p>
													<span class="date_article"><?= extraireDate($article['DATE_ARTICLE']) ?></span>
												</div>
											</div>
										</a>
									</div>
								<?php endforeach; ?>
							</div>

							<!-- Pages navigation --> 
							<div class="row pt-3">
								<div class="col-3">
									<a href="home">« Retour à l'accueil</a>
								</div>
								<div class="col-3 offset-6">
									<?php if ($startPage !== 1): ?>
										<a href="actualites">« premier</a> ...
									<?php endif; ?>

									<?php for ($i = $startPage; $i <= $endPage; $i++): ?>
										<?php if ($i === $numPage): ?>
											<span class="text-warning fw-bold" aria-current="page"><?= $i ?></span>
										<?php else: ?>
											<a href="actualites?page=<?= $i ?>" aria-label="Aller à la page <?= $i ?>"><?= $i ?></a>
										<?php endif; ?>
									<?php endfor; ?>

									<?php if ($endPage < $nbPages): ?>
										... <a href="actualites?page=<?= $nbPages ?>">dernier »</a>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endif; ?>
				<?php else: ?>
					<h2 class="text-center">Erreur : Mauvais numéro de page ou page inexistante</h2>
				<?php endif; ?>
			</div>
		</main>