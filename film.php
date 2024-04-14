<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/connexionBDD.php';
require $racine . '/CineToile/util/extraire_date.php';
require $racine . '/CineToile/util/afficher_notes_etoiles.php';

$film = false;

if(isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$id = (int) $_GET['id']; // On récupére l'id du film
	$film = executeReqFetchArgs($dbh,'SELECT * FROM films WHERE id_film = ?',[$id]); // On récupére les données de ce film

	if($film) {
		$date_sortie = obtenirDate($film['DATE_SORTIE']);
		$duree = !empty($film['DUREE']) ? ($film['DUREE'] > 60 ? intdiv($film['DUREE'],60) . ' h ' . $film['DUREE'] % 60 . ' min' : $film['DUREE'] . ' min') : '';

		$realisateurs = executeReqFetchAllArgs($dbh,'SELECT individus.id_individu, NOM FROM individus INNER JOIN films_individus ON individus.id_individu = films_individus.id_individu WHERE id_film = ? AND role = "R"',[$id]);
		$categories = executeReqFetchAllArgs($dbh,'SELECT * FROM categories INNER JOIN films_categories ON categories.id_categorie = films_categories.id_categorie WHERE id_film = ?',[$id]);

		$moyenne = executeReqFetchArgs($dbh,'SELECT AVG(NOTE) FROM films_avis WHERE id_film = ?',[$id])['AVG(NOTE)']; // On récupére la moyenne de toutes les notes de ce film
		$nb_avis = executeReqFetchArgs($dbh,'SELECT COUNT(*) AS nb_avis FROM films_avis WHERE id_film = ?',[$id])['nb_avis']; // On récupére le nombre d'avis asscoiés à ce film
		$note_utilisateur = $connecte ? executeReqFetchArgs($dbh,'SELECT NOTE FROM films_avis WHERE id_film = ? AND pseudo = ?',[$id,$_SESSION['pseudo']]) : false;

		$acteurs = executeReqFetchAllArgs($dbh,'SELECT individus.id_individu, NOM, PHOTO FROM individus INNER JOIN films_individus ON individus.id_individu = films_individus.id_individu WHERE id_film = ? AND role = "A"',[$id]);
		$les_avis = executeReqFetchAllArgs($dbh,'SELECT utilisateurs.pseudo, NOTE, DATE_AVIS, CRITIQUE FROM films_avis INNER JOIN utilisateurs ON films_avis.pseudo = utilisateurs.pseudo WHERE id_film = ? ORDER BY DATE_AVIS DESC LIMIT 3',[$id]);
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head><?php require $racine . '/CineToile/base/head.php'; ?>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const mois_fr = ["janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre"];

				function obtenirDate(valeur) {
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

				function obtenirEtoiles(nombre,taillePx) {
					let resultat = '';
					for(let i = 0; i < 5; i++) {
						let starType = 'black_star.png';
						if(nombre !== null) {
							if(nombre > i) {
								if(nombre < i + 1) {
									starType = 'half_star.png';
								} else {
									starType = 'gold_star.png';
								}
							}
						}
						resultat += '<img class="img-fluid" style="height: ' + taillePx + 'px;" src="img/etoiles/' + starType + '" alt="' + starType + '"/> ';
					}
					return resultat;
				}

				const rateInputs = document.querySelectorAll('.rate input');

				rateInputs.forEach(async function(input) {
					input.addEventListener('click', async function() {
						let rating = this.value / 2;
						let formData = new FormData();
						formData.append('id', <?php if ($film) { echo $id; } ?>);
						formData.append('stars', rating);

						try {
							const response = await fetch('/CineToile/util/notes', {
								method: 'POST',
								body: formData
							});

							if(!response.ok) {
								throw new Error('Echec de la réponse de la requête fetch : ' + response.status);
							}

							const result_data = await response.json();

							let etoiles_spect = obtenirEtoiles(result_data[0].moyenne, 18);
							etoiles_spect += result_data[0].moyenne + ' (' + result_data[0].nb_avis + ' avis)';

							let j = 1;
							let nb_avis_extrait = result_data[1].length;
							let les_avis = '';
							for(let avis of result_data[1]) {
								let date_avis = obtenirDate(avis.DATE_AVIS, mois_fr);
								les_avis += obtenirEtoiles(avis.NOTE, 18);
								les_avis += avis.NOTE + ' publié le ' + date_avis + ' par ' + avis.pseudo + '<br />';
								les_avis += '<div class="text-break">' + (avis.CRITIQUE != null ? nl2br(avis.CRITIQUE) : '') + '</div>';
								les_avis += '<br />';
								if(j < nb_avis_extrait) {
									les_avis += '<div class="border-bottom"></div>';
								}
								j++;
							}
							document.getElementById('moyenne').innerHTML = etoiles_spect;
							document.getElementById('note_utilisateur').innerHTML = rating;
							document.getElementById('les_3_derniers_avis').innerHTML = les_avis;
						} catch (err) {
							console.log('Echec du résultat de la requête fetch : ' + err);
						}
					});
				});
			});
		</script>
	</head>
	<body class="bg-dark"><?php require $racine . '/CineToile/base/barremenu.php'; ?>
		<div class="container bg-white" style="min-height:100vh;">
			<?php if($film) { ?>
				<div class="row">
					<!-- Affiche du film -->
					<div class="text-center col-lg-3 col-md-8 offset-md-2 col-10 offset-1">
						<img class="img-fluid" src="img/affiches/<?= $film['AFFICHE'] ?>" alt="affiche <?= $film['TITRE'] ?>"/>
					</div>

					<!-- Informations sur le film -->
					<div class="col-lg-5 offset-lg-0 col-md-8 offset-md-2 col-10 offset-1">
						<h1 id="titreFilm" class="text-uppercase"><?= $film['TITRE'] ?></h1>
						<table class="table">
							<tr>
								<th scope="row">Date de sortie</th>
								<td><?= $date_sortie ?></td>
							</tr>
							<tr>
								<th scope="row">Durée</th>
								<td><?= $duree ?></td>
							</tr>
							<tr>
								<th scope="row">Pays prod</th>
								<td><?= $film['PAYS'] ?></td>
							</tr>
							<tr>
								<th scope="row">Réalisateur<?php if(count($realisateurs) > 1) { echo 's'; } ?></th>
								<td>
									<?php foreach($realisateurs as $realisateur) { ?>
										<a href="individu?id=<?= $realisateur['id_individu'] ?>"><?= $realisateur['NOM'] ?></a>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<th scope="row">Catégories</th>
								<td>
									<?php foreach($categories as $categorie) { ?>
										<a href="categories_recherche?id=<?= $categorie['id_categorie'] ?>"><?= $categorie['NOM'] ?></a>
									<?php } ?>
								</td>
							</tr>
						</table>

						<!-- Moyenne des spectateurs et note utilisateur -->
						<div class="row">
							<div class="col-6">
								<div><b>SPECTATEURS</b></div>
								<div id="moyenne">
									<?php echo obtenir_etoiles($moyenne,18);
									if($moyenne != null) { echo $moyenne; }
									echo ' (' . $nb_avis . ' avis)' ?>
								</div>
							</div>
							<div class="col-6">
								<?php if($connecte) { ?>
									<div><b>VOTRE NOTE</b></div>
									<fieldset class="rate">
										<?php for($i = 10; $i > 0; $i--) { ?>
											<input type="radio" id="rating<?= $i ?>" name="rating" value="<?= $i ?>" <?php if($note_utilisateur) { if($i == $note_utilisateur['NOTE'] * 2) { echo ' checked'; } } ?>/>
											<label <?php if($i % 2 != 0) { echo 'class="half"'; } ?> for="rating<?= $i ?>" title="<?php echo intdiv($i,2); if($i % 2 != 0) { echo '.5'; } echo ' étoile'; if($i > 3) { echo 's'; } ?>"></label>
										<?php } ?>
									</fieldset>
									<span id="note_utilisateur"><?php if($note_utilisateur) { echo $note_utilisateur['NOTE']; } ?></span>
								<?php } ?>
							</div>
						</div>

						<!-- Liens vers les avis et pour rédiger son propre avis -->
						<div class="row">
							<div class="col-6">
								<a href="film_voir_avis?id=<?= $id ?>">Voir les avis sur ce film »</a>
							</div>
							<div class="col-6">
								<?php if($connecte) { ?>
									<a href="film_rediger_avis?id=<?= $id ?>">Rédiger votre critique sur ce film »</a>
								<?php } else { ?>
									Vous devez être connecté pour pouvoir rédiger un avis sur ce film.
								<?php } ?>
							</div>
						</div>	
					</div>
				</div>

				<!-- Synopsis -->
				<div class="row pt-3">
					<div class="col-md-8 offset-md-2 col-10 offset-1">
						<h1 class="champFilm border-bottom">Synopsis</h1>
						<div class="text-break"><?= nl2br($film['SYNOPSIS']) ?></div>
					</div>
				</div>

				<!-- Bande-annonce -->
				<div class="row pt-3">
					<div class="col-md-8 offset-md-2 col-10 offset-1">
						<h1 class="champFilm border-bottom">Bande-annonce</h1>
						<?php if(!empty($film['LIEN_YOUTUBE'])) { ?>
							<div class="ratio ratio-16x9">
								<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/<?= $film['LIEN_YOUTUBE'] ?>" title="YouTube video player" style="border: 0;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
							</div>
						<?php } ?>
					</div>
				</div>

				<!-- Acteurs principaux -->
				<div class="row pt-3">
					<div class="col-md-8 offset-md-2 col-10 offset-1">
						<h1 class="champFilm border-bottom">Acteurs principaux</h1>
						<div class="row">
							<?php foreach($acteurs as $acteur) { ?>
								<div class="col-lg-3 col-md-4 col-6">
									<a href="individu?id=<?= $acteur['id_individu'] ?>"><img class="img-fluid" src="img/individus/<?= $acteur['PHOTO'] ?>" alt="photo de <?= $acteur['NOM'] ?>"><?= $acteur['NOM'] ?></a>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>

				<!-- Les 3 derniers avis -->
				<div class="row pt-3">
					<div class="col-md-8 offset-md-2 col-10 offset-1">
						<h1 class="champFilm border-bottom">Les 3 derniers avis</h1>
						<div id="les_3_derniers_avis">
							<?php $j = 1;
							$nb_avis_extrait = count($les_avis);
							foreach($les_avis as $avis) {
								$date_avis = obtenirDate($avis['DATE_AVIS']);
								echo obtenir_etoiles($avis['NOTE'],18);
								echo $avis['NOTE'] . ' publié le ' . $date_avis . ' par ' . $avis['pseudo']; ?>
								<br />
								<div class="text-break"><?= nl2br($avis['CRITIQUE']) ?></div>
								<br />
								<?php if($j < $nb_avis_extrait) { ?>
									<div class="border-bottom"></div>
								<?php }
								$j++;
							} ?>
						</div>
					</div>
				</div>
			<?php } else { ?>
				<h2 class="text-center">Erreur : Mauvais ID film ou film inexistant</h2>
			<?php } ?>
		</div>
	</body>
</html>