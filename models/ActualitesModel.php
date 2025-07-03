<?php
/**
 * Modèle de la page actualites.php (/actualites?page=X)
 *
 * @version 4.0
 * @author Alain Barbier alias "Metroidzeta" (backend/frontend)
 * @author Roger Huang (frontend/design)
 */

final class ActualitesModel {
	private PDO $dbh;
	public const NB_ARTICLES_PAR_PAGE = 8;

	public function __construct(PDO $dbh) {
		$this->dbh = $dbh;
	}

	/**
	 * Récupère le nombre total d'articles dans la base de données
	 */
	public function getNbArticles(): int {
		$stmt = $this->dbh->query('SELECT COUNT(*) FROM articles');
		return (int) $stmt->fetchColumn();
	}

	/**
	 * Récupère les articles pour une page donnée
	 */
	public function getArticlesByPage(int $page): array {
		if ($page < 1) $page = 1;

		$offset = ($page - 1) * self::NB_ARTICLES_PAR_PAGE;

		$stmt = $this->dbh->prepare(
				'SELECT *
				FROM articles
				ORDER BY DATE_ARTICLE DESC LIMIT :limit OFFSET :offset'
		);
		$stmt->bindValue(':limit', self::NB_ARTICLES_PAR_PAGE, PDO::PARAM_INT);
		$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}