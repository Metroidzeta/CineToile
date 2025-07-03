<?php
/**
 * Modèle de la page home.php (/home)
 *
 * @version 4.0
 * @author Alain Barbier alias "Metroidzeta" (fullstack: backend/frontend)
 * @author Roger Huang (frontend/design)
 */

final class HomeModel {

	private PDO $dbh;

	public function __construct(PDO $dbh) {
		$this->dbh = $dbh;
	}

	/**
	 * Retourne les éléments du carousel.
	 */
	public function getCarouselItems(): array {
		return [
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
	}

	/**
	 * Récupère les 12 premiers films de la base de données avec leurs réalisateurs.
	 */
	public function getFilms(): array {
		return executeReqFetchAll($this->dbh,
			'SELECT f.id_film, f.TITRE, f.AFFICHE, GROUP_CONCAT(i.NOM ORDER BY i.NOM SEPARATOR ", ") AS realisateurs
			 FROM films f
			 LEFT JOIN films_individus fi ON f.id_film = fi.id_film AND fi.role = "R"
			 LEFT JOIN individus i ON fi.id_individu = i.id_individu
			 GROUP BY f.id_film
			 LIMIT 12'
		);
	}

	/**
	 * Récupère les 6 derniers articles.
	 */
	public function getDerniersArticles(): array {
		return executeReqFetchAll($this->dbh,
			'SELECT *
			 FROM articles
			 ORDER BY DATE_ARTICLE DESC
			 LIMIT 6'
		);
	}
}