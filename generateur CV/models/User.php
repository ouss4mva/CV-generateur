<?php

class User
{
    private $id;
    private $nom;
    private $prenom;
    private $email;
    private $motDePasse;
    private $dateCreation;
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
        $this->email = strtolower(trim($email));
        return $this;
    }

    public function getMotDePasse()
    {
        return $this->motDePasse;
    }

    public function setMotDePasse($motDePasse)
    {
        $this->motDePasse = $motDePasse;
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

    public function hydrater(array $donnees)
    {
        if (isset($donnees['id'])) {
            $this->setId($donnees['id']);
        }
        if (isset($donnees['nom'])) {
            $this->setNom($donnees['nom']);
        }
        if (isset($donnees['prenom'])) {
            $this->setPrenom($donnees['prenom']);
        }
        if (isset($donnees['email'])) {
            $this->setEmail($donnees['email']);
        }
        if (isset($donnees['mot_de_passe'])) {
            $this->setMotDePasse($donnees['mot_de_passe']);
        }
        if (isset($donnees['date_creation'])) {
            $this->setDateCreation($donnees['date_creation']);
        }
        return $this;
    }

    public function definirMotDePasseSecurise($motDePasseClair)
    {
        $this->motDePasse = password_hash($motDePasseClair, PASSWORD_DEFAULT);
        return $this;
    }

    public function verifierMotDePasse($motDePasseClair)
    {
        if ($this->motDePasse === null) {
            return false;
        }
        return password_verify($motDePasseClair, $this->motDePasse);
    }

    public function emailExiste($email)
    {
        $requete = $this->db->prepare('SELECT id FROM user WHERE email = :email LIMIT 1');
        $requete->execute(array(':email' => strtolower(trim($email))));
        return $requete->fetch() !== false;
    }

    public function creer()
    {
        $requete = $this->db->prepare('INSERT INTO user (nom, prenom, email, mot_de_passe) VALUES (:nom, :prenom, :email, :mot_de_passe)');
        $resultat = $requete->execute(array(
            ':nom' => $this->nom,
            ':prenom' => $this->prenom,
            ':email' => $this->email,
            ':mot_de_passe' => $this->motDePasse
        ));
        if ($resultat) {
            $this->id = (int) $this->db->lastInsertId();
        }
        return $resultat;
    }

    public function trouverParEmail($email)
    {
        $requete = $this->db->prepare('SELECT * FROM user WHERE email = :email LIMIT 1');
        $requete->execute(array(':email' => strtolower(trim($email))));
        $donnees = $requete->fetch();
        if ($donnees === false) {
            return null;
        }
        $utilisateur = new User();
        return $utilisateur->hydrater($donnees);
    }

    public function trouverParId($id)
    {
        $requete = $this->db->prepare('SELECT * FROM user WHERE id = :id LIMIT 1');
        $requete->execute(array(':id' => (int) $id));
        $donnees = $requete->fetch();
        if ($donnees === false) {
            return null;
        }
        $utilisateur = new User();
        return $utilisateur->hydrater($donnees);
    }

    public function getNomComplet()
    {
        return trim($this->prenom . ' ' . $this->nom);
    }
}
