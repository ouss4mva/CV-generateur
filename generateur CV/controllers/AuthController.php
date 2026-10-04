<?php

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function afficherAuth()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?action=dashboard');
            exit;
        }
        $erreurs = isset($_SESSION['erreurs']) ? $_SESSION['erreurs'] : array();
        $succes = isset($_SESSION['succes']) ? $_SESSION['succes'] : '';
        $anciennesValeurs = isset($_SESSION['anciennes_valeurs']) ? $_SESSION['anciennes_valeurs'] : array();
        $ongletActif = isset($_SESSION['onglet_actif']) ? $_SESSION['onglet_actif'] : 'connexion';
        unset($_SESSION['erreurs'], $_SESSION['succes'], $_SESSION['anciennes_valeurs'], $_SESSION['onglet_actif']);
        require __DIR__ . '/../views/auth.php';
    }

    public function inscription()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=auth');
            exit;
        }

        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
        $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $motDePasse = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';
        $confirmation = isset($_POST['confirmation']) ? $_POST['confirmation'] : '';

        $erreurs = array();

        if (mb_strlen($nom) < 4) {
            $erreurs[] = 'Le nom doit contenir au moins 4 caracteres.';
        }
        if (mb_strlen($prenom) < 4) {
            $erreurs[] = 'Le prenom doit contenir au moins 4 caracteres.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreurs[] = "L'adresse email saisie n'est pas valide.";
        }
        if (mb_strlen($motDePasse) < 6) {
            $erreurs[] = 'Le mot de passe doit contenir au moins 6 caracteres.';
        }
        if ($motDePasse !== $confirmation) {
            $erreurs[] = 'Les deux mots de passe ne correspondent pas.';
        }
        if (empty($erreurs) && $this->userModel->emailExiste($email)) {
            $erreurs[] = 'Cette adresse email est deja utilisee par un autre compte.';
        }

        if (!empty($erreurs)) {
            $_SESSION['erreurs'] = $erreurs;
            $_SESSION['onglet_actif'] = 'inscription';
            $_SESSION['anciennes_valeurs'] = array('nom' => $nom, 'prenom' => $prenom, 'email' => $email);
            header('Location: index.php?action=auth');
            exit;
        }

        $utilisateur = new User();
        $utilisateur->setNom($nom)
            ->setPrenom($prenom)
            ->setEmail($email)
            ->definirMotDePasseSecurise($motDePasse);

        if ($utilisateur->creer()) {
            $_SESSION['user_id'] = $utilisateur->getId();
            $_SESSION['user_nom'] = $utilisateur->getNomComplet();
            header('Location: index.php?action=dashboard');
            exit;
        }

        $_SESSION['erreurs'] = array("La creation du compte a echoue. Veuillez reessayer.");
        $_SESSION['onglet_actif'] = 'inscription';
        header('Location: index.php?action=auth');
        exit;
    }

    public function connexion()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=auth');
            exit;
        }

        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $motDePasse = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';
        $erreurs = array();

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreurs[] = "L'adresse email saisie n'est pas valide.";
        }
        if ($motDePasse === '') {
            $erreurs[] = 'Le mot de passe est obligatoire.';
        }

        if (empty($erreurs)) {
            $utilisateur = $this->userModel->trouverParEmail($email);
            if ($utilisateur === null || !$utilisateur->verifierMotDePasse($motDePasse)) {
                $erreurs[] = 'Email ou mot de passe incorrect.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $utilisateur->getId();
                $_SESSION['user_nom'] = $utilisateur->getNomComplet();
                header('Location: index.php?action=dashboard');
                exit;
            }
        }

        $_SESSION['erreurs'] = $erreurs;
        $_SESSION['onglet_actif'] = 'connexion';
        $_SESSION['anciennes_valeurs'] = array('email' => $email);
        header('Location: index.php?action=auth');
        exit;
    }

    public function deconnexion()
    {
        $_SESSION = array();
        session_destroy();
        header('Location: index.php?action=home');
        exit;
    }

    public static function utilisateurConnecte()
    {
        if (!isset($_SESSION['user_id']) || (int) $_SESSION['user_id'] <= 0) {
            return false;
        }
        if ((new User())->trouverParId((int) $_SESSION['user_id']) === null) {
            $_SESSION = array();
            return false;
        }
        return true;
    }

    public static function protegerRoute()
    {
        if (!self::utilisateurConnecte()) {
            $_SESSION['erreurs'] = array('Vous devez vous connecter pour acceder a cette page.');
            header('Location: index.php?action=auth');
            exit;
        }
    }
}
