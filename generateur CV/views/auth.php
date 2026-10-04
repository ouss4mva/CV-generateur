<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion et inscription</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-auth">
<header class="barre-navigation">
    <a class="logo" href="index.php?action=home">CV<span> Generateur</span></a>
    <nav class="liens-navigation">
        <a class="bouton bouton-clair" href="index.php?action=home">Retour a l'accueil</a>
    </nav>
</header>

<main class="conteneur-auth">
    <section class="carte-auth">
        <h1 class="titre-auth">Acceder a votre espace</h1>
        <p class="sous-titre-auth">Connectez-vous ou creez un compte pour generer vos CV.</p>

        <div class="onglets-auth">
            <button type="button" class="onglet-auth<?php echo $ongletActif === 'connexion' ? ' actif' : ''; ?>" data-cible="panneau-connexion">Connexion</button>
            <button type="button" class="onglet-auth<?php echo $ongletActif === 'inscription' ? ' actif' : ''; ?>" data-cible="panneau-inscription">Inscription</button>
        </div>

        <?php if (!empty($erreurs)): ?>
            <div class="alerte alerte-erreur">
                <ul>
                    <?php foreach ($erreurs as $erreur): ?>
                        <li><?php echo echapper($erreur); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($succes)): ?>
            <div class="alerte alerte-succes"><?php echo echapper($succes); ?></div>
        <?php endif; ?>

        <div id="panneau-connexion" class="panneau-auth<?php echo $ongletActif === 'connexion' ? ' actif' : ''; ?>">
            <form method="post" action="index.php?action=connexion" novalidate>
                <div class="champ">
                    <label for="connexion-email">Adresse email</label>
                    <input type="email" id="connexion-email" name="email" placeholder="@gmail.com" value="<?php echo isset($anciennesValeurs['email']) ? echapper($anciennesValeurs['email']) : ''; ?>" required>
                </div>
                <div class="champ">
                    <label for="connexion-mot-de-passe">Mot de passe</label>
                    <input type="password" id="connexion-mot-de-passe" name="mot_de_passe" placeholder="Votre mot de passe" required>
                </div>
                <button type="submit" class="bouton bouton-principal bouton-bloc">Se connecter</button>
            </form>
        </div>

        <div id="panneau-inscription" class="panneau-auth<?php echo $ongletActif === 'inscription' ? ' actif' : ''; ?>">
            <form method="post" action="index.php?action=inscription" novalidate>
                <div class="ligne-champs">
                    <div class="champ">
                        <label for="inscription-nom">Nom</label>
                        <input type="text" id="inscription-nom" name="nom" placeholder="Au moins 4 caracteres" value="<?php echo isset($anciennesValeurs['nom']) ? echapper($anciennesValeurs['nom']) : ''; ?>" required>
                    </div>
                    <div class="champ">
                        <label for="inscription-prenom">Prenom</label>
                        <input type="text" id="inscription-prenom" name="prenom" placeholder="Au moins 4 caracteres" value="<?php echo isset($anciennesValeurs['prenom']) ? echapper($anciennesValeurs['prenom']) : ''; ?>" required>
                    </div>
                </div>
                <div class="champ">
                    <label for="inscription-email">Adresse email</label>
                    <input type="email" id="inscription-email" name="email" placeholder="@gmail.com" value="<?php echo isset($anciennesValeurs['email']) ? echapper($anciennesValeurs['email']) : ''; ?>" required>
                </div>
                <div class="ligne-champs">
                    <div class="champ">
                        <label for="inscription-mot-de-passe">Mot de passe</label>
                        <input type="password" id="inscription-mot-de-passe" name="mot_de_passe" placeholder="6 caracteres minimum" required>
                    </div>
                    <div class="champ">
                        <label for="inscription-confirmation">Confirmation</label>
                        <input type="password" id="inscription-confirmation" name="confirmation" placeholder="Repetez le mot de passe" required>
                    </div>
                </div>
                <button type="submit" class="bouton bouton-principal bouton-bloc">Creer mon compte</button>
            </form>
        </div>
    </section>
</main>

<script>
    document.querySelectorAll('.onglet-auth').forEach(function (onglet) {
        onglet.addEventListener('click', function () {
            document.querySelectorAll('.onglet-auth').forEach(function (element) {
                element.classList.remove('actif');
            });
            document.querySelectorAll('.panneau-auth').forEach(function (element) {
                element.classList.remove('actif');
            });
            onglet.classList.add('actif');
            document.getElementById(onglet.dataset.cible).classList.add('actif');
        });
    });
</script>
</body>
</html>
