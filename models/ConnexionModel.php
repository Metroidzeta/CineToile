<?php
/**
 * Modèle de la page connexion.php (/connexion)
 *
 * @version 4.0
 * @author Alain Barbier alias "Metroidzeta" (backend/frontend)
 * @author Roger Huang (frontend/design)
 */

final class ConnexionModel {

	private PDO $dbh;

	public function __construct(PDO $dbh) {
		$this->dbh = $dbh;
	}

	/**
	 * Récupère l'utilisateur à partir de son email
	 */
	public function getUtilisateurDepuisEmail(string $email): ?array {
		$stmt = $this->dbh->prepare('SELECT * FROM utilisateurs WHERE EMAIL = ?');
		$stmt->execute([$email]);
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		return $user ?: null;
	}
}