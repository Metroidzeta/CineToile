<?php
/**
 * Modèle de la page individu.php (/individu?id=X)
 *
 * @version 4.0
 * @author Alain Barbier alias "Metroidzeta" (backend/frontend)
 * @author Roger Huang (frontend/design)
 */

final class IndividuModel {

	private PDO $dbh;

	public function __construct(PDO $dbh) {
		$this->dbh = $dbh;
	}

	/**
	 * Récupère un individu à partir de son id
	 */
	public function getIndividu(int $id): array|false {
		$stmt = $this->dbh->prepare('SELECT * FROM individus WHERE id_individu = ?');
		$stmt->execute([$id]);
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Récupère la filmographie d'un individu à partir de son id
	 */
	public function getFilmsIndividu(int $id): array {
		$stmt = $this->dbh->prepare(
			'SELECT DISTINCT f.id_film, f.TITRE, f.AFFICHE
				FROM films f
				INNER JOIN films_individus fi ON f.id_film = fi.id_film
				WHERE fi.id_individu = ?'
			);
		$stmt->execute([$id]);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Récupère le genre de l'individu sous forme de string
	 */
	public static function getGenre(string $code): string {
		return match($code) {
			'H' => 'Homme',
			'F' => 'Femme',
			default => '',
		};
	}

	/**
	 * Calcule l'âge de l'individu à partir de sa date de naissance
	 */
	public static function calculerAge(?string $dateNaissance): ?int {
		if (empty($dateNaissance)) return null;

		try {
			$dt = new DateTime($dateNaissance);
			$now = new DateTime();
			return $dt->diff($now)->y;
		} catch (Exception $e) {
			return null;
		}
	}
}