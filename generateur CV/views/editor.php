<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editeur de CV</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/templates.css">
</head>
<body class="page-editeur">
<header class="barre-editeur">
    <div class="groupe-gauche">
        <a class="bouton bouton-clair bouton-petit" href="index.php?action=dashboard">Retour</a>
        <input type="text" id="champ-titre" class="champ-titre" maxlength="150" placeholder="Titre du CV" value="<?php echo $cv !== null ? echapper($cv->getTitre()) : 'Mon CV professionnel'; ?>">
        <span class="etiquette-modele-actif">Modele : <?php echo echapper($template->getNom()); ?></span>
    </div>
    <div class="groupe-droite">
        <span id="indicateur-sauvegarde" class="indicateur-sauvegarde"></span>
        <button type="button" id="bouton-enregistrer" class="bouton bouton-principal">Enregistrer</button>
        <button type="button" id="bouton-pdf" class="bouton bouton-secondaire">Telecharger PDF</button>
    </div>
</header>

<main class="espace-editeur">
    <section class="panneau-formulaire">
        <form id="formulaire-cv" autocomplete="off">
            <input type="hidden" id="id-cv" name="id_cv" value="<?php echo $cv !== null ? (int) $cv->getId() : ''; ?>">
            <input type="hidden" id="id-template" name="id_template" value="<?php echo (int) $template->getId(); ?>">

            <fieldset class="bloc-formulaire">
                <legend>Informations personnelles</legend>
                <div class="ligne-champs">
                    <div class="champ">
                        <label for="champ-prenom">Prenom</label>
                        <input type="text" id="champ-prenom" data-champ="prenom" maxlength="100" placeholder="Said" value="<?php echo $cv !== null ? echapper($cv->getPrenom()) : ''; ?>">
                    </div>
                    <div class="champ">
                        <label for="champ-nom">Nom</label>
                        <input type="text" id="champ-nom" data-champ="nom" maxlength="100" placeholder="Saadouni" value="<?php echo $cv !== null ? echapper($cv->getNom()) : ''; ?>">
                    </div>
                </div>
                <div class="ligne-champs">
                    <div class="champ">
                        <label for="champ-email">Email</label>
                        <input type="email" id="champ-email" data-champ="email" maxlength="150" placeholder="said.sadouni@estn.ma" value="<?php echo $cv !== null ? echapper($cv->getEmail()) : ''; ?>">
                    </div>
                    <div class="champ">
                        <label for="champ-telephone">Telephone</label>
                        <input type="text" id="champ-telephone" data-champ="telephone" inputmode="numeric" pattern="[0-9]{10,15}" maxlength="15" placeholder="0612345678" value="<?php echo $cv !== null ? echapper($cv->getTelephone()) : ''; ?>">
                    </div>
                </div>
                <div class="champ">
                    <label for="champ-adresse">Adresse</label>
                    <input type="text" id="champ-adresse" data-champ="adresse" maxlength="200" placeholder="Selouane Rue 339 bloc 4 Nador" value="<?php echo $cv !== null ? echapper($cv->getAdresse()) : ''; ?>">
                </div>
                <?php if ($template->getAvecPhoto()): ?>
                    <div class="champ">
                        <label for="champ-photo">Photo de profil</label>
                        <input type="file" id="champ-photo" accept="image/png, image/jpeg, image/webp">
                        <input type="hidden" id="supprimer-photo" value="0">
                        <button type="button" id="bouton-retirer-photo" class="bouton bouton-petit bouton-clair">Retirer la photo</button>
                    </div>
                <?php endif; ?>
            </fieldset>

            <fieldset class="bloc-formulaire">
                <legend>Poste recherche</legend>
                <div class="champ">
                    <label for="champ-poste">Intitule du poste</label>
                    <input type="text" id="champ-poste" data-champ="poste_recherche" maxlength="150" placeholder="Developpeuse web full stack" value="<?php echo $cv !== null ? echapper($cv->getPosteRecherche()) : ''; ?>">
                </div>
            </fieldset>

            <fieldset class="bloc-formulaire">
                <legend>Experiences professionnelles</legend>
                <div id="liste-experiences" class="liste-entrees"></div>
                <button type="button" id="ajouter-experience" class="bouton bouton-ajout">Ajouter une experience</button>
            </fieldset>

            <fieldset class="bloc-formulaire">
                <legend>Formations</legend>
                <div id="liste-formations" class="liste-entrees"></div>
                <button type="button" id="ajouter-formation" class="bouton bouton-ajout">Ajouter une formation</button>
            </fieldset>

            <fieldset class="bloc-formulaire">
                <legend>Competences</legend>
                <div class="champ">
                    <label for="champ-competences">Une competence par ligne</label>
                    <textarea id="champ-competences" data-champ="competences" rows="5" placeholder="PHP oriente objet&#10;MySQL&#10;JavaScript"><?php echo $cv !== null ? echapper($cv->getCompetences()) : ''; ?></textarea>
                </div>
            </fieldset>

            <fieldset class="bloc-formulaire">
                <legend>Langues</legend>
                <div class="champ">
                    <label for="champ-langues">Une langue par ligne</label>
                    <textarea id="champ-langues" data-champ="langues" rows="4" placeholder="Francais : langue maternelle&#10;Anglais : courant"><?php echo $cv !== null ? echapper($cv->getLangues()) : ''; ?></textarea>
                </div>
            </fieldset>
        </form>
    </section>

    <section class="panneau-apercu">
        <div class="cadre-apercu">
            <article id="apercu-cv" class="cv-document cv-<?php echo echapper($template->getCheminFichier()); ?>">
                <header class="cv-entete">
                    <?php if ($template->getAvecPhoto()): ?>
                        <div class="cv-photo">
                            <img id="apercu-photo" src="<?php echo $cv !== null && $cv->getPhoto() ? echapper($cv->getPhoto()) : 'assets/img/photo-placeholder.svg'; ?>" alt="Photo de profil">
                        </div>
                    <?php endif; ?>
                    <div class="cv-identite">
                        <h1 class="cv-nom">
                            <span data-apercu="prenom"><?php echo $cv !== null ? echapper($cv->getPrenom()) : 'Prenom'; ?></span>
                            <span data-apercu="nom"><?php echo $cv !== null ? echapper($cv->getNom()) : 'Nom'; ?></span>
                        </h1>
                        <p class="cv-poste" data-apercu="poste_recherche"><?php echo $cv !== null ? echapper($cv->getPosteRecherche()) : 'Poste recherche'; ?></p>
                    </div>
                </header>

                <div class="cv-corps">
                    <aside class="cv-colonne-laterale">
                        <section class="cv-bloc">
                            <h2>Contact</h2>
                            <ul class="cv-contact">
                                <li data-apercu="email"><?php echo $cv !== null ? echapper($cv->getEmail()) : ''; ?></li>
                                <li data-apercu="telephone"><?php echo $cv !== null ? echapper($cv->getTelephone()) : ''; ?></li>
                                <li data-apercu="adresse"><?php echo $cv !== null ? echapper($cv->getAdresse()) : ''; ?></li>
                            </ul>
                        </section>
                        <section class="cv-bloc">
                            <h2>Competences</h2>
                            <ul id="apercu-competences" class="cv-liste-simple"></ul>
                        </section>
                        <section class="cv-bloc">
                            <h2>Langues</h2>
                            <ul id="apercu-langues" class="cv-liste-simple"></ul>
                        </section>
                    </aside>
                    <div class="cv-colonne-principale">
                        <section class="cv-bloc">
                            <h2>Experiences professionnelles</h2>
                            <div id="apercu-experiences" class="cv-liste-detaillee"></div>
                        </section>
                        <section class="cv-bloc">
                            <h2>Formations</h2>
                            <div id="apercu-formations" class="cv-liste-detaillee"></div>
                        </section>
                    </div>
                </div>
            </article>
        </div>
    </section>
</main>

<script>
    window.donneesInitiales = {
        experiences: <?php echo json_encode($experiences, JSON_UNESCAPED_UNICODE); ?>,
        formations: <?php echo json_encode($formations, JSON_UNESCAPED_UNICODE); ?>,
        avecPhoto: <?php echo $template->getAvecPhoto() ? 'true' : 'false'; ?>
    };
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="assets/js/editor.js"></script>
</body>
</html>
