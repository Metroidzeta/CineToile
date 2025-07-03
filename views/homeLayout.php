<?php 
/**
 * Layout pour la page home (/home), spécifique à cette page à cause de l'import CSS du carousel
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
		<!-- Owl Carousel -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css"/>
	</head>
	<body>
		<?php require $racine . '/CineToile/base/barremenu.php'; ?>

		<?php include $view; ?>

		<?php require $racine . '/CineToile/base/footer.php'; ?>
	</body>
</html>