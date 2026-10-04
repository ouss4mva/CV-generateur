<?php

session_start();

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Template.php';
require_once __DIR__ . '/models/Cv.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/CvController.php';

function echapper($valeur)
{
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}

$action = isset($_GET['action']) ? $_GET['action'] : 'home';

try {
    $authController = new AuthController();
    $cvController = new CvController();

    switch ($action) {
        case 'auth':
            $authController->afficherAuth();
            break;
        case 'inscription':
            $authController->inscription();
            break;
        case 'connexion':
            $authController->connexion();
            break;
        case 'logout':
            $authController->deconnexion();
            break;
        case 'dashboard':
            $cvController->tableauDeBord();
            break;
        case 'editor':
            $cvController->editeur();
            break;
        case 'enregistrer':
            $cvController->enregistrer();
            break;
        case 'supprimer':
            $cvController->supprimer();
            break;
        case 'home':
        default:
            $cvController->accueil();
            break;
    }
} catch (PDOException $exception) {
    http_response_code(500);
    if ($action === 'enregistrer') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('succes' => false, 'message' => 'Erreur de connexion a la base de donnees.'));
    } else {
        echo '<h1>Erreur de connexion a la base de donnees</h1><p>Verifiez la configuration dans config/Database.php.</p>';
    }
}
