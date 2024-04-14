<?php // v1.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) et Roger Huang (frontend/design)
// v3.0 @author Alain Barbier alias "Metroidzeta" (backend/frontend) ?>

		<nav class="navbar navbar-expand-md navbar-dark bg-dark bg-gradient fixed-top">
			<div class="container-fluid">
				<a class="navbar-brand" href="home">Ciné-toile</a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav me-auto mb-2 mb-lg-0">
						<li class="nav-item">
							<a class="nav-link" aria-current="page" href="categories">Catégories</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" aria-current="page" href="actualites">Actualités</a>
						</li>
					</ul>
					<div class="navbar-text" style="margin-right:5px;">
						<?php if($connecte) { ?>Bienvenue <a href="profil" style="color: #808080;"><?php echo $_SESSION['pseudo']; ?></a><?php } ?>
					</div>
					<a class="nav-link" id="texteSeConnecter" href="<?php if($connecte) { ?>util/deconnexion<?php } else { ?>connexion<?php } ?>">
						<?php if($connecte) { ?>Se déconnecter<?php } else { ?>Se connecter<?php } ?>
					</a>
					<form method="GET" action="rechercher" class="d-flex">
						<input class="form-control me-2" id="autocomplete" type="search" name="q" placeholder="un individu, un film..." aria-label="Rechercher"/>
						<button class="btn btn-warning" type="submit">Rechercher</button>
					</form>
				</div>
			</div>
		</nav>