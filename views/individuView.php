<?php
/**
 * View de la page individu.php (/individu?id=X)
 *
 * @version 4.0
 * @author Alain Barbier alias "Metroidzeta" (fullstack: backend/frontend)
 * @author Roger Huang (frontend/design)
 */
?>
		<main class="bg-dark">
			<div class="container bg-white" style="min-height:100vh;">
				<?php if ($individu): ?>
					<div class="row">
						<!-- Photo -->
						<div class="text-center col-lg-3 col-md-8 offset-md-2 col-10 offset-1">
							<img class="img-fluid" loading="lazy" src="/CineToile/img/individus/<?= htmlspecialchars(basename(rawurldecode($individu['PHOTO']))) ?>" alt="photo de <?= htmlspecialchars($individu['NOM']) ?>"/>
						</div>

						<!-- Informations  -->
						<section class="col-lg-5 offset-lg-0 col-md-8 offset-md-2 col-10 offset-1">
							<h1 id="nomIndividu"><?= htmlspecialchars($individu['NOM']) ?></h1>
							<table class="table">
								<tr>
									<th scope="row">Date de naissance</th>
									<td><?= $dateNaissanceAffichee ?></td>
								</tr>
								<tr>
									<th scope="row">Métiers</th>
									<td><?= extraireMetiers($individu['METIERS'], $individu['GENRE']) ?></td>
								</tr>
								<tr>
									<th scope="row">Nationalité</th>
									<td><?= htmlspecialchars($individu['NATIONALITE']) ?></td>
								</tr>
								<tr>
									<th scope="row">Age</th>
									<td><?= $age !== null ? $age . ' ans' : '' ?></td>
								</tr>
								<tr>
									<th scope="row">Genre</th>
									<td><?= $genre ?></td>
								</tr>
							</table>
						</section>
					</div>

					<!-- Biographie -->
					<section class="row pt-3">
						<div class="col-md-8 offset-md-2 col-10 offset-1">
							<h1 class="champIndividu border-bottom">Biographie</h1>
							<div class="text-break"><?= nl2br(htmlspecialchars($individu['BIOGRAPHIE'])) ?></div>
						</div>
					</section>

					<!-- Filmographie  -->
					<section class="row pt-3">
						<div class="col-md-8 offset-md-2 col-10 offset-1">
							<h1 class="champIndividu border-bottom">Filmographie</h1>
							<div class="row">
								<?php foreach($films as $film): ?>
									<div class="col-lg-3 col-md-4 col-6">
										<a href="film?id=<?= (int) $film['id_film'] ?>">
											<img class= "img-fluid" loading="lazy" src="/CineToile/img/affiches/<?= htmlspecialchars(basename(rawurldecode($film['AFFICHE']))) ?>" alt="affiche de <?= htmlspecialchars($film['TITRE']) ?>">
											<?= htmlspecialchars($film['TITRE']) ?>
										</a>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</section>
				<?php else: ?>
					<h2 class="text-center">Erreur : Mauvais ID individu ou individu inexistant</h2>
				<?php endif; ?>
			</div>
		</main>