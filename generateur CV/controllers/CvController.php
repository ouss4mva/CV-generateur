<?php

class CvController
{
    private $cvModel;
    private $templateModel;
    private $limites = array(
        'titre' => 150,
        'nom' => 100,
        'prenom' => 100,
        'email' => 150,
        'telephone' => 15,
        'adresse' => 200,
        'poste_recherche' => 150
    );

    public function __construct()
    {
        $this->cvModel = new Cv();
        $this->templateModel = new Template();
    }

    public function accueil()
    {
        require __DIR__ . '/../views/home.php';
    }

    public function tableauDeBord()
    {
        AuthController::protegerRoute();
        $templates = $this->templateModel->trouverTous();
        $historique = $this->cvModel->trouverParUtilisateur($_SESSION['user_id']);
        $onglet = isset($_GET['onglet']) ? $_GET['onglet'] : 'modeles';
        $message = isset($_SESSION['succes']) ? $_SESSION['succes'] : '';
        unset($_SESSION['succes']);
        require __DIR__ . '/../views/dashboard.php';
    }

    public function editeur()
    {
        AuthController::protegerRoute();

        $cv = null;
        $template = null;

        if (isset($_GET['id_cv'])) {
            $cv = $this->cvModel->trouverParId((int) $_GET['id_cv'], $_SESSION['user_id']);
            if ($cv === null) {
                header('Location: index.php?action=dashboard&onglet=historique');
                exit;
            }
            $template = $this->templateModel->trouverParId($cv->getIdTemplate());
        } elseif (isset($_GET['id_template'])) {
            $template = $this->templateModel->trouverParId((int) $_GET['id_template']);
        }

        if ($template === null) {
            header('Location: index.php?action=dashboard');
            exit;
        }

        $experiences = $cv !== null ? $cv->getListeExperiences() : array();
        $formations = $cv !== null ? $cv->getListeFormations() : array();
        require __DIR__ . '/../views/editor.php';
    }

    public function enregistrer()
    {
        header('Content-Type: application/json; charset=utf-8');
        $nouvellePhoto = null;

        try {
            if (!AuthController::utilisateurConnecte()) {
                $this->repondre(false, 'Session expiree, veuillez vous reconnecter.');
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->repondre(false, 'Requete invalide.');
            }
            if (empty($_POST)) {
                $this->repondre(false, 'Les donnees envoyees sont trop volumineuses. Reduisez la taille de la photo.');
            }

            $idUser = (int) $_SESSION['user_id'];
            $idCv = isset($_POST['id_cv']) && $_POST['id_cv'] !== '' ? (int) $_POST['id_cv'] : 0;
            $idTemplate = isset($_POST['id_template']) ? (int) $_POST['id_template'] : 0;

            if ($this->templateModel->trouverParId($idTemplate) === null) {
                $this->repondre(false, 'Le modele selectionne est introuvable.');
            }

            $champs = array();
            foreach ($this->limites as $cle => $maximum) {
                $valeur = isset($_POST[$cle]) ? trim($_POST[$cle]) : '';
                if (mb_strlen($valeur) > $maximum) {
                    $this->repondre(false, 'Le champ "' . str_replace('_', ' ', $cle) . '" ne doit pas depasser ' . $maximum . ' caracteres.');
                }
                $champs[$cle] = $valeur;
            }
            if ($champs['telephone'] !== '' && !preg_match('/^[0-9]{10,15}$/', $champs['telephone'])) {
                $this->repondre(false, 'Le telephone doit contenir uniquement des chiffres (10 a 15 chiffres).');
            }
            if ($champs['titre'] === '') {
                $champs['titre'] = 'CV sans titre';
            }

            if ($idCv > 0) {
                $cv = $this->cvModel->trouverParId($idCv, $idUser);
                if ($cv === null) {
                    $this->repondre(false, "Ce CV n'existe pas ou ne vous appartient pas.");
                }
            } else {
                $cv = new Cv();
            }

            $anciennePhoto = $cv->getPhoto();
            $photo = $anciennePhoto;
            $nouvellePhoto = $this->traiterPhoto();
            if ($nouvellePhoto !== null) {
                $photo = $nouvellePhoto;
            } elseif (isset($_POST['supprimer_photo']) && $_POST['supprimer_photo'] === '1') {
                $photo = null;
            }

            $cv->setIdUser($idUser)
                ->setIdTemplate($idTemplate)
                ->setTitre($champs['titre'])
                ->setNom($champs['nom'])
                ->setPrenom($champs['prenom'])
                ->setEmail($champs['email'])
                ->setTelephone($champs['telephone'])
                ->setAdresse($champs['adresse'])
                ->setPhoto($photo)
                ->setPosteRecherche($champs['poste_recherche'])
                ->setExperiences($this->normaliserJson(isset($_POST['experiences']) ? $_POST['experiences'] : ''))
                ->setFormations($this->normaliserJson(isset($_POST['formations']) ? $_POST['formations'] : ''))
                ->setCompetences(isset($_POST['competences']) ? trim($_POST['competences']) : '')
                ->setLangues(isset($_POST['langues']) ? trim($_POST['langues']) : '');

            $resultat = $idCv > 0 ? $cv->modifier() : $cv->creer();
            if (!$resultat) {
                throw new RuntimeException("L'enregistrement du CV a echoue.");
            }

            if ($anciennePhoto !== null && $anciennePhoto !== $photo) {
                $this->supprimerFichierPhoto($anciennePhoto);
            }

            $this->repondre(true, 'Votre CV a bien ete enregistre.', array(
                'id_cv' => $cv->getId(),
                'photo' => $cv->getPhoto()
            ));
        } catch (RuntimeException $exception) {
            $this->supprimerFichierPhoto($nouvellePhoto);
            $this->repondre(false, $exception->getMessage());
        } catch (PDOException $exception) {
            $this->supprimerFichierPhoto($nouvellePhoto);
            $this->repondre(false, "L'enregistrement du CV a echoue : erreur de base de donnees.");
        }
    }

    public function supprimer()
    {
        AuthController::protegerRoute();
        if (isset($_GET['id_cv'])) {
            $cv = $this->cvModel->trouverParId((int) $_GET['id_cv'], $_SESSION['user_id']);
            if ($cv !== null) {
                $this->supprimerFichierPhoto($cv->getPhoto());
                $this->cvModel->supprimer($cv->getId(), $_SESSION['user_id']);
                $_SESSION['succes'] = 'Le CV a ete supprime avec succes.';
            }
        }
        header('Location: index.php?action=dashboard&onglet=historique');
        exit;
    }

    private function repondre($succes, $message, array $donnees = array())
    {
        echo json_encode(array_merge(array('succes' => $succes, 'message' => $message), $donnees), JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function traiterPhoto()
    {
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $fichier = $_FILES['photo'];
        if ($fichier['error'] === UPLOAD_ERR_INI_SIZE || $fichier['error'] === UPLOAD_ERR_FORM_SIZE || $fichier['size'] > 2097152) {
            throw new RuntimeException('La photo ne doit pas depasser 2 Mo.');
        }
        if ($fichier['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Le televersement de la photo a echoue.');
        }

        $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, array('jpg', 'jpeg', 'png', 'webp'), true) || getimagesize($fichier['tmp_name']) === false) {
            throw new RuntimeException('La photo doit etre une image JPG, PNG ou WEBP.');
        }

        $dossier = __DIR__ . '/../uploads/';
        if (!is_dir($dossier) && !mkdir($dossier, 0755, true)) {
            throw new RuntimeException("Le dossier d'envoi des photos est inaccessible.");
        }

        $nomFichier = 'photo_' . $_SESSION['user_id'] . '_' . uniqid() . '.' . $extension;
        if (!move_uploaded_file($fichier['tmp_name'], $dossier . $nomFichier)) {
            throw new RuntimeException("La photo n'a pas pu etre enregistree.");
        }
        return 'uploads/' . $nomFichier;
    }

    private function supprimerFichierPhoto($chemin)
    {
        if ($chemin !== null && $chemin !== '' && strpos($chemin, 'uploads/') === 0 && is_file(__DIR__ . '/../' . $chemin)) {
            unlink(__DIR__ . '/../' . $chemin);
        }
    }

    private function normaliserJson($valeur)
    {
        $donnees = json_decode($valeur, true);
        if (!is_array($donnees)) {
            return json_encode(array(), JSON_UNESCAPED_UNICODE);
        }
        return json_encode($donnees, JSON_UNESCAPED_UNICODE);
    }
}
