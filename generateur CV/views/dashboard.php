<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-dashboard">
<header class="barre-navigation">
    <a class="logo" href="index.php?action=home">CV<span> Generateur</span></a>
    <nav class="liens-navigation">
        <span class="utilisateur-connecte">Bonjour, <?php echo echapper($_SESSION['user_nom']); ?></span>
        <a class="bouton bouton-clair" href="index.php?action=logout">Deconnexion</a>
    </nav>
</header>

<main class="conteneur-dashboard">
    <h1 class="titre-page">Mon espace</h1>
    <p class="description-page">Choisissez un modele pour creer un nouveau CV ou reprenez un document deja enregistre.</p>

    <?php if (!empty($message)): ?>
        <div class="alerte alerte-succes"><?php echo echapper($message); ?></div>
    <?php endif; ?>

    <div class="onglets-dashboard">
        <button type="button" class="onglet-dashboard<?php echo $onglet === 'modeles' ? ' actif' : ''; ?>" data-cible="section-modeles">Modeles de CV</button>
        <button type="button" class="onglet-dashboard<?php echo $onglet === 'historique' ? ' actif' : ''; ?>" data-cible="section-historique">Mon Historique</button>
    </div>

    <section id="section-modeles" class="section-dashboard<?php echo $onglet === 'modeles' ? ' actif' : ''; ?>">
        <div class="grille-modeles">
            <?php foreach ($templates as $modele): ?>
                <article class="carte-modele">
                    <div class="apercu-modele">
                        <img src="<?php echo echapper($modele->getImageApercu()); ?>" alt="Apercu du modele <?php echo echapper($modele->getNom()); ?>">
                    </div>
                    <div class="contenu-carte-modele">
                        <h3><?php echo echapper($modele->getNom()); ?></h3>
                        <p class="etiquette-modele"><?php echo $modele->getAvecPhoto() ? 'Avec photo' : 'Sans photo'; ?></p>
                        <a class="bouton bouton-principal bouton-bloc" href="index.php?action=editor&amp;id_template=<?php echo (int) $modele->getId(); ?>">Choisir ce modele</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="section-historique" class="section-dashboard<?php echo $onglet === 'historique' ? ' actif' : ''; ?>">
        <?php if (empty($historique)): ?>
            <div class="etat-vide">
                <h3>Aucun CV enregistre pour le moment</h3>
                <p>Selectionnez un modele dans l'onglet "Modeles de CV" pour commencer.</p>
            </div>
        <?php else: ?>
            <table class="tableau-historique">
                <thead>
                <tr>
                    <th>Titre</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($historique as $cvEnregistre): ?>
                    <tr>
                        <td><?php echo echapper($cvEnregistre->getTitre()); ?></td>
                        <td><?php echo echapper(date('d/m/Y H:i', strtotime($cvEnregistre->getDateModification() !== null ? $cvEnregistre->getDateModification() : $cvEnregistre->getDateCreation()))); ?></td>
                        <td class="cellule-actions">
                            <a class="bouton bouton-petit bouton-secondaire" href="index.php?action=editor&amp;id_cv=<?php echo (int) $cvEnregistre->getId(); ?>">Modifier</a>
                            <a class="bouton bouton-petit bouton-danger" href="index.php?action=supprimer&amp;id_cv=<?php echo (int) $cvEnregistre->getId(); ?>" onclick="return confirm('Voulez-vous vraiment supprimer ce CV ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>

<script>
    document.querySelectorAll('.onglet-dashboard').forEach(function (onglet) {
        onglet.addEventListener('click', function () {
            document.querySelectorAll('.onglet-dashboard').forEach(function (element) {
                element.classList.remove('actif');
            });
            document.querySelectorAll('.section-dashboard').forEach(function (element) {
                element.classList.remove('actif');
            });
            onglet.classList.add('actif');
            document.getElementById(onglet.dataset.cible).classList.add('actif');
        });
    });
</script>
</body>
</html>
