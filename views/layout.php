<?php 
/**
 * Layout de base des pages de CineToile
 *
 * @version 4.0
 * @author Alain Barbier alias "Metroidzeta" (fullstack: backend/frontend)
 * @author Roger Huang (frontend/design)
 */
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php require $racine . '/CineToile/base/head.php'; ?>
	</head>
	<body>
		<?php require $racine . '/CineToile/base/barremenu.php'; ?>

		<?php include $view; ?>

		<?php require $racine . '/CineToile/base/footer.php'; ?>
	</body>
</html>