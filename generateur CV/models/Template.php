<?php

class Template
{
    private $id;
    private $nom;
    private $imageApercu;
    private $cheminFichier;
    private $avecPhoto;
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

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom)
    {
        $this->nom = trim($nom);
        return $this;
    }

    public function getImageApercu()
    {
        return $this->imageApercu;
    }

    public function setImageApercu($imageApercu)
    {
        $this->imageApercu = $imageApercu;
        return $this;
    }

    public function getCheminFichier()
    {
        return $this->cheminFichier;
    }

    public function setCheminFichier($cheminFichier)
    {
        $this->cheminFichier = $cheminFichier;
        return $this;
    }

    public function getAvecPhoto()
    {
        return (bool) $this->avecPhoto;
    }

    public function setAvecPhoto($avecPhoto)
    {
        $this->avecPhoto = (bool) $avecPhoto;
        return $this;
    }

    public function hydrater(array $donnees)
    {
        if (isset($donnees['id'])) {
            $this->setId($donnees['id']);
        }
        if (isset($donnees['nom'])) {
            $this->setNom($donnees['nom']);
        }
        if (isset($donnees['image_apercu'])) {
            $this->setImageApercu($donnees['image_apercu']);
        }
        if (isset($donnees['chemin_fichier'])) {
            $this->setCheminFichier($donnees['chemin_fichier']);
        }
        if (isset($donnees['avec_photo'])) {
            $this->setAvecPhoto($donnees['avec_photo']);
        }
        return $this;
    }

    public function trouverTous()
    {
        $requete = $this->db->prepare('SELECT * FROM template ORDER BY id ASC');
        $requete->execute();
        $liste = array();
        foreach ($requete->fetchAll() as $donnees) {
            $modele = new Template();
            $liste[] = $modele->hydrater($donnees);
        }
        return $liste;
    }

    public function trouverParId($id)
    {
        $requete = $this->db->prepare('SELECT * FROM template WHERE id = :id LIMIT 1');
        $requete->execute(array(':id' => (int) $id));
        $donnees = $requete->fetch();
        if ($donnees === false) {
            return null;
        }
        $modele = new Template();
        return $modele->hydrater($donnees);
    }
}
