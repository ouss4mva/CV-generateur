<?php

class Cv
{
    private $id;
    private $idUser;
    private $idTemplate;
    private $titre;
    private $nom;
    private $prenom;
    private $email;
    private $telephone;
    private $adresse;
    private $photo;
    private $posteRecherche;
    private $experiences;
    private $formations;
    private $competences;
    private $langues;
    private $dateCreation;
    private $dateModification;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    public function getIdUser()
    {
        return $this->idUser;
    }

    public function setIdUser($idUser)
    {
        $this->idUser = (int) $idUser;
        return $this;
    }

    public function getIdTemplate()
    {
        return $this->idTemplate;
    }

    public function setIdTemplate($idTemplate)
    {
        $this->idTemplate = (int) $idTemplate;
        return $this;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($titre)
    {
        $this->titre = trim($titre);
        return $this;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom)
    {
        $this->nom = trim($nom);
        return $this;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function setPrenom($prenom)
    {
        $this->prenom = trim($prenom);
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = trim($email);
        return $this;
    }

    public function getTelephone()
    {
        if ($this->telephone === null || $this->telephone === '') {
            return '';
        }
        return str_pad((string) $this->telephone, 10, '0', STR_PAD_LEFT);
    }

    public function setTelephone($telephone)
    {
        $telephone = trim((string) $telephone);
        $this->telephone = $telephone === '' ? null : $telephone;
        return $this;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function setAdresse($adresse)
    {
        $this->adresse = trim($adresse);
        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;
        return $this;
    }

    public function getPosteRecherche()
    {
        return $this->posteRecherche;
    }

    public function setPosteRecherche($posteRecherche)
    {
        $this->posteRecherche = trim($posteRecherche);
        return $this;
    }

    public function getExperiences()
    {
        return $this->experiences;
    }

    public function setExperiences($experiences)
    {
        $this->experiences = $experiences;
        return $this;
    }

    public function getFormations()
    {
        return $this->formations;
    }

    public function setFormations($formations)
    {
        $this->formations = $formations;
        return $this;
    }

    public function getCompetences()
    {
        return $this->competences;
    }

    public function setCompetences($competences)
    {
        $this->competences = $competences;
        return $this;
    }

    public function getLangues()
    {
        return $this->langues;
    }

    public function setLangues($langues)
    {
        $this->langues = $langues;
        return $this;
    }

    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getDateModification()
    {
        return $this->dateModification;
    }

    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;
        return $this;
    }

    public function hydrater(array $donnees)
    {
        $correspondances = array(
            'id' => 'setId',
            'id_user' => 'setIdUser',
            'id_template' => 'setIdTemplate',
            'titre' => 'setTitre',
            'nom' => 'setNom',
            'prenom' => 'setPrenom',
            'email' => 'setEmail',
            'telephone' => 'setTelephone',
            'adresse' => 'setAdresse',
            'photo' => 'setPhoto',
            'poste_recherche' => 'setPosteRecherche',
            'experiences' => 'setExperiences',
            'formations' => 'setFormations',
            'competences' => 'setCompetences',
            'langues' => 'setLangues',
            'date_creation' => 'setDateCreation',
            'date_modification' => 'setDateModification'
        );
        foreach ($correspondances as $colonne => $methode) {
            if (array_key_exists($colonne, $donnees) && $donnees[$colonne] !== null) {
                $this->$methode($donnees[$colonne]);
            }
        }
        return $this;
    }

    public function creer()
    {
        $requete = $this->db->prepare('INSERT INTO cv (id_user, id_template, titre, nom, prenom, email, telephone, adresse, photo, poste_recherche, experiences, formations, competences, langues) VALUES (:id_user, :id_template, :titre, :nom, :prenom, :email, :telephone, :adresse, :photo, :poste_recherche, :experiences, :formations, :competences, :langues)');
        $resultat = $requete->execute($this->parametres());
        if ($resultat) {
            $this->id = (int) $this->db->lastInsertId();
        }
        return $resultat;
    }

    public function modifier()
    {
        $requete = $this->db->prepare('UPDATE cv SET id_template = :id_template, titre = :titre, nom = :nom, prenom = :prenom, email = :email, telephone = :telephone, adresse = :adresse, photo = :photo, poste_recherche = :poste_recherche, experiences = :experiences, formations = :formations, competences = :competences, langues = :langues, date_modification = NOW() WHERE id = :id AND id_user = :id_user');
        $parametres = $this->parametres();
        $parametres[':id'] = $this->id;
        return $requete->execute($parametres);
    }

    private function parametres()
    {
        return array(
            ':id_user' => $this->idUser,
            ':id_template' => $this->idTemplate,
            ':titre' => $this->titre,
            ':nom' => $this->nom,
            ':prenom' => $this->prenom,
            ':email' => $this->email,
            ':telephone' => $this->telephone,
            ':adresse' => $this->adresse,
            ':photo' => $this->photo,
            ':poste_recherche' => $this->posteRecherche,
            ':experiences' => $this->experiences,
            ':formations' => $this->formations,
            ':competences' => $this->competences,
            ':langues' => $this->langues
        );
    }

    public function trouverParId($id, $idUser)
    {
        $requete = $this->db->prepare('SELECT * FROM cv WHERE id = :id AND id_user = :id_user LIMIT 1');
        $requete->execute(array(':id' => (int) $id, ':id_user' => (int) $idUser));
        $donnees = $requete->fetch();
        if ($donnees === false) {
            return null;
        }
        $cv = new Cv();
        return $cv->hydrater($donnees);
    }

    public function trouverParUtilisateur($idUser)
    {
        $requete = $this->db->prepare('SELECT * FROM cv WHERE id_user = :id_user ORDER BY id DESC');
        $requete->execute(array(':id_user' => (int) $idUser));
        $liste = array();
        foreach ($requete->fetchAll() as $donnees) {
            $cv = new Cv();
            $liste[] = $cv->hydrater($donnees);
        }
        return $liste;
    }

    public function supprimer($id, $idUser)
    {
        $requete = $this->db->prepare('DELETE FROM cv WHERE id = :id AND id_user = :id_user');
        return $requete->execute(array(':id' => (int) $id, ':id_user' => (int) $idUser));
    }

    public function getListeExperiences()
    {
        return $this->decoder($this->experiences);
    }

    public function getListeFormations()
    {
        return $this->decoder($this->formations);
    }

    private function decoder($valeur)
    {
        if ($valeur === null || $valeur === '') {
            return array();
        }
        $donnees = json_decode($valeur, true);
        return is_array($donnees) ? $donnees : array();
    }
}
