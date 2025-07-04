<?php
/**
 * Utilitaire : Vérification de la connexion d'une session
 * Version : v3.0
 * Auteur : Alain Barbier alias "Metroidzeta" (backend/frontend), Roger Huang (frontend/design)
 */

session_set_cookie_params([
	'httponly' => true,
	'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'), // true si HTTPS
	'samesite' => 'Strict'
]);

if (session_status() === PHP_SESSION_NONE) session_start();
$connecte = isset($_SESSION['pseudo'], $_SESSION['EMAIL']) && !empty($_SESSION['pseudo']) && !empty($_SESSION['EMAIL']);
?>