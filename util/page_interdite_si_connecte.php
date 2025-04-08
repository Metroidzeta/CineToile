<?php
/**
 * Utilitaire : Page interdite si connecté (redirection vers le home)
 * Version : v3.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */

if($connecte) {
	header('Location:/CineToile/home');
	die();
}
?>