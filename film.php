<?php
/**
 * Page : Affichage de la page d'un film (/film?id=X)
 * Version : v3.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/extraire_date.php';
require $racine . '/CineToile/util/afficher_notes_etoiles.php';

$film = false;

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$id = (int) $_GET['id'];

	$film = executeReqFetchArgs($dbh, // On récupére les données de ce film
		'SELECT *
		FROM films
		WHERE id_film = ?',[$id]
	);

	if ($film) {
		$duree = !empty($film['DUREE']) ? ($film['DUREE'] > 60 ? intdiv($film['DUREE'],60) . ' h ' . $film['DUREE'] % 60 . ' min' : $film['DUREE'] . ' min') : '';

		$realisateurs = executeReqFetchAllArgs($dbh,
			'SELECT i.id_individu, i.NOM
			FROM individus i
			INNER JOIN films_individus fi ON i.id_individu = fi.id_individu
			WHERE fi.id_film = ? AND fi.role = "R"',[$id]
		);

		$categories = executeReqFetchAllArgs($dbh,
			'SELECT *
			FROM categories c
			INNER JOIN films_categories fc ON c.id_categorie = fc.id_categorie
			WHERE fc.id_film = ?',[$id]
		);

		$req_moyenne_et_nbAvis = executeReqFetchArgs($dbh,
			'SELECT AVG(NOTE) as moyenne, COUNT(*) as nbAvis
			FROM films_avis
			WHERE id_film = ?',[$id]
		);
		$moyenne = $req_moyenne_et_nbAvis['moyenne']; // On récupére la moyenne des notes postées sur ce film
		$nbAvis = $req_moyenne_et_nbAvis['nbAvis']; // On récupére le nombre d'avis postés sur ce film

		$note_utilisateur = $connecte ? 
			executeReqFetchArgs($dbh,
				'SELECT NOTE
				FROM films_avis 
				WHERE id_film = ? AND pseudo = ?',[$id, $_SESSION['pseudo']]
			) : false;

		$acteurs = executeReqFetchAllArgs($dbh,
			'SELECT i.id_individu, i.NOM, i.PHOTO
			FROM individus i
			INNER JOIN films_individus fi ON i.id_individu = fi.id_individu
			WHERE fi.id_film = ? AND fi.role = "A"',[$id]
		);

		$les_avis = executeReqFetchAllArgs($dbh,
			'SELECT u.pseudo, fa.NOTE, fa.DATE_AVIS, fa.CRITIQUE
			FROM films_avis fa
			INNER JOIN utilisateurs u ON fa.pseudo = u.pseudo
			WHERE fa.id_film = ? ORDER BY DATE_AVIS DESC LIMIT 3',[$id]
		);

		$nbAvis_extrait = count($les_avis);
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php require $racine . '/CineToile/base/head.php'; ?>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const mois_fr = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];

				function extraireDate(valeur) {
					let date_extraite = '';
					if(valeur != '') {
						const date_tab = valeur.split('-');
						date_extraite = parseInt(date_tab[2]) + ' ' + mois_fr[parseInt(date_tab[1]) - 1] + ' ' + date_tab[0];
					}
					return date_extraite;
				}

				function nl2br(str) {
					return str.replace(/(?:\r\n|\r|\n)/g,'<br />');
				}

				function getStars(nombre, pxSize) {
					if (!pxSize) return '';

					let html = '';
					for(let i = 0; i < 5; i++) {
						let starType = 'black_star.png';
						if(nombre !== null && nombre > i) {
							starType = (nombre < i + 1) ? 'half_star.png' : 'gold_star.png';
						}
						html += `<img class="img-fluid" style="height: ${pxSize}px;" src="img/etoiles/${starType}" alt="${starType}"/> `;
					}
					return html;
				}

				const rateInputs = document.querySelectorAll('.rate input');

				rateInputs.forEach(input => {
					input.addEventListener('click', async () => {
						const rating = input.value / 2;
						let formData = new FormData();
						formData.append('id', <?= htmlspecialchars($film ? $id : '') ?>);
						formData.append('stars', rating);

						try {
							const response = await fetch('/CineToile/util/notes', {
								method: 'POST',
								body: formData
							});

							if(!response.ok) {
								throw new Error(`Echec de la réponse fetch : ${response.status}`);
							}

							const result_data = await response.json();

							let etoiles_spect = getStars(result_data[0].moyenne, 18) + result_data[0].moyenne + ' (' + result_data[0].nb_avis + ' avis)';

							const nb_avis_extrait = result_data[1].length;
							let avis_html = '';
							result_data[1].forEach((avis, index) => {
								const date_avis = extraireDate(avis.DATE_AVIS, mois_fr);
								avis_html += getStars(avis.NOTE, 18) + avis.NOTE + ' publié le ' + date_avis + ' par ' + avis.pseudo;
								avis_html += '<br/><div class="text-break">';
								avis_html += avis.CRITIQUE != null ? nl2br(avis.CRITIQUE) : '';
								avis_html += '</div><br/>';
								if(index + 1 < nb_avis_extrait) {
									avis_html += '<div class="border-bottom"></div>';
								}
							});
							document.getElementById('moyenne').innerHTML = etoiles_spect;
							document.getElementById('note_utilisateur').innerHTML = rating;
							document.getElementById('les_3_derniers_avis').innerHTML = avis_html;
						} catch (err) {
							console.log('Echec du résultat de la requête fetch : ' + err);
						}
					});
				});
			});
		</script>
	</head>
	<body class="bg-dark">
		<?php require $racine . '/CineToile/base/barremenu.php'; ?>

		<div class="container bg-white" style="min-height:100vh;">
			<?php if ($film): ?>
				<div class="row">
					<!-- Affiche du film -->
					<div class="text-center col-lg-3 col-md-8 offset-md-2 col-10 offset-1">
						<img class="img-fluid" src="/CineToile/img/affiches/<?= basename(rawurldecode($film['AFFICHE'])) ?>" alt="affiche <?= htmlspecialchars($film['TITRE']) ?>"/>
					</div>

					<!-- Informations sur le film -->
					<div class="col-lg-5 offset-lg-0 col-md-8 offset-md-2 col-10 offset-1">
						<h1 id="titreFilm" class="text-uppercase"><?= htmlspecialchars($film['TITRE']) ?></h1>
						<table class="table">
							<tr>
								<th scope="row">Date de sortie</th>
								<td><?= extraireDate($film['DATE_SORTIE']) ?></td>
							</tr>
							<tr>
								<th scope="row">Durée</th>
								<td><?= $duree ?></td>
							</tr>
							<tr>
								<th scope="row">Pays prod</th>
								<td><?= htmlspecialchars($film['PAYS']) ?></td>
							</tr>
							<tr>
								<th scope="row">Réalisateur<?= (count($realisateurs) > 1) ? 's' : '' ?></th>
								<td>
									<?php foreach ($realisateurs as $realisateur): ?>
										<a href="individu?id=<?= (int) $realisateur['id_individu'] ?>"><?= htmlspecialchars($realisateur['NOM']) ?></a>
									<?php endforeach; ?>
								</td>
							</tr>
							<tr>
								<th scope="row">Catégories</th>
								<td>
									<?php foreach ($categories as $categorie): ?>
										<a href="categories_recherche?id=<?= (int) $categorie['id_categorie'] ?>"><?= htmlspecialchars($categorie['NOM']) ?></a>
									<?php endforeach; ?>
								</td>
							</tr>
						</table>

						<!-- Moyenne des spectateurs et note utilisateur -->
						<div class="row">
							<div class="col-6">
								<div><b>SPECTATEURS</b></div>
								<div id="moyenne">
									<?php echo getStars($moyenne, 18);
									if ($moyenne != null) { echo $moyenne; }
									echo ' (' . $nbAvis . ' avis)' ?>
								</div>
							</div>
							<div class="col-6">
								<?php if($connecte): ?>
									<div><b>VOTRE NOTE</b></div>
									<fieldset class="rate">
										<?php for ($i = 10; $i > 0; $i--): ?>
											<input type="radio" id="rating<?= $i ?>" name="rating" value="<?= $i ?>" <?= $note_utilisateur ? (($i == $note_utilisateur['NOTE'] * 2) ? 'checked' : '') : '' ?>/>
											<label <?= ($i % 2 != 0) ? 'class="half"' : '' ?> for="rating<?= $i ?>" title="<?= intdiv($i, 2) . (($i % 2 !== 0) ? '.5' : '') ?> star<?= ($i > 3) ? 's' : '' ?>"></label>
										<?php endfor; ?>
									</fieldset>
									<span id="note_utilisateur"><?= $note_utilisateur ? $note_utilisateur['NOTE'] : '' ?></span>
								<?php endif; ?>
							</div>
						</div>

						<!-- Liens vers les avis et pour rédiger son propre avis -->
						<div class="row">
							<div class="col-6">
								<a href="film_voir_avis?id=<?= $id ?>">Voir les avis sur ce film »</a>
							</div>
							<div class="col-6">
								<?php if ($connecte): ?>
									<a href="film_rediger_avis?id=<?= $id ?>">Rédiger votre critique sur ce film »</a>
								<?php else: ?>
									Vous devez être connecté pour pouvoir rédiger un avis sur ce film.
								<?php endif; ?>
							</div>
						</div>	
					</div>
				</div>

				<!-- Synopsis -->
				<div class="row pt-3">
					<div class="col-md-8 offset-md-2 col-10 offset-1">
						<h1 class="champFilm border-bottom">Synopsis</h1>
						<div class="text-break"><?= nl2br(htmlspecialchars($film['SYNOPSIS'])) ?></div>
					</div>
				</div>

				<!-- Bande-annonce -->
				<div class="row pt-3">
					<div class="col-md-8 offset-md-2 col-10 offset-1">
						<h1 class="champFilm border-bottom">Bande-annonce</h1>
						<?php if (!empty($film['LIEN_YOUTUBE'])): ?>
							<div class="ratio ratio-16x9">
								<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/<?= $film['LIEN_YOUTUBE'] ?>" title="YouTube video player" style="border: 0;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- Acteurs principaux -->
				<div class="row pt-3">
					<div class="col-md-8 offset-md-2 col-10 offset-1">
						<h1 class="champFilm border-bottom">Acteurs principaux</h1>
						<div class="row">
							<?php foreach ($acteurs as $acteur): ?>
								<div class="col-lg-3 col-md-4 col-6">
									<a href="individu?id=<?= (int) $acteur['id_individu'] ?>">
										<img class="img-fluid" src="/CineToile/img/individus/<?= basename(rawurldecode($acteur['PHOTO'])) ?>" alt="photo de <?= htmlspecialchars($acteur['NOM']) ?>">
										<?= htmlspecialchars($acteur['NOM']) ?>
									</a>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<!-- Les 3 derniers avis -->
				<div class="row pt-3">
					<div class="col-md-8 offset-md-2 col-10 offset-1">
						<h1 class="champFilm border-bottom">Les 3 derniers avis</h1>
						<div id="les_3_derniers_avis">
							<?php foreach ($les_avis as $index => $avis): ?>
								<?= getStars($avis['NOTE'], 18) . $avis['NOTE'] . ' publié le ' . extraireDate($avis['DATE_AVIS']) . ' par ' . $avis['pseudo'] ?>
								<br/><div class="text-break">
									<?= $avis['CRITIQUE'] ? nl2br(htmlspecialchars($avis['CRITIQUE'])) : '' ?>
								</div><br/>
								<?php if ($index + 1 < $nbAvis_extrait): ?>
									<div class="border-bottom"></div>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			<?php else: ?>
				<h2 class="text-center">Erreur : Mauvais ID film ou film inexistant</h2>
			<?php endif; ?>
		</div>
	</body>
</html>