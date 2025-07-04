<?php
/**
 * View de la page connexion.php (/connexion)
 *
 * @version 4.0
 * @author Alain Barbier alias "Metroidzeta" (fullstack: backend/frontend)
 * @author Roger Huang (frontend/design)
 */
?>
		<main class="body_formulaire">
			<div class="container">
				<div id="formulaire" class="text-center">
					<h2>Connexion</h2>
					<form action="connexion" method="POST" autocomplete="off">
						<!-- Champ caché du token CSRF -->
						<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />
						<div class="mb-3">
							<input type="email" name="email" class="form-control" placeholder="Email" value="<?= !empty($msgErr) ? htmlspecialchars($email) : '' ?>" required />
						</div>
						<div class="mb-3">
							<input type="password" name="password" class="form-control" placeholder="Mot de passe" required />
						</div>
						<div class="mb-3">
							<input type="submit" class="btn btn-danger" value="Se connecter"/>
						</div>
					</form>
					<p>Pas encore inscrit ? <a class="text-danger" href="inscription">Inscrivez-vous !</a></p>
					<?php if (!empty($msgErr)): ?>
						<div class="alert alert-danger"><?= htmlspecialchars($msgErr) ?></div>
					<?php endif; ?>
				</div>
			</div>
		</main>