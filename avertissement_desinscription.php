<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend)

$racine = $_SERVER['DOCUMENT_ROOT'];

require $racine . '/CineToile/util/verifSession.php';
require $racine . '/CineToile/util/page_interdite_si_non_connecte.php';
?>
<!DOCTYPE html>
<html lang="fr">
	<head><?php require $racine . '/CineToile/base/head.php'; ?></head>
	<body><?php require $racine . '/CineToile/base/barremenu.php'; ?>
		<div class="container text-center" style="min-height:100vh;">
			<b>Souhaitez-vous réellement vous désinscrire du site ?</b>
			<div class="row pt-4">
				<div class="col-4 offset-2">
					<a class="btn btn-danger" style="color:white;" href="util/desinscription" role="button">Oui</a>
				</div>
				<div class="col-4">
					<a class="btn btn-success" style="color:white;" href="profil" role="button">Non</a>		
				</div>
			</div>
		</div>
		<?php require $racine . '/CineToile/base/footer.php'; ?>
	</body>
</html>