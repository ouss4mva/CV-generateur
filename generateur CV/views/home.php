<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generateur de CV</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-accueil">
<header class="barre-navigation">
    <a class="logo" href="index.php?action=home">CV<span> Generateur</span></a>
    <nav class="liens-navigation">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a class="bouton bouton-secondaire" href="index.php?action=dashboard">Mon espace</a>
            <a class="bouton bouton-clair" href="index.php?action=logout">Deconnexion</a>
        <?php else: ?>
            <a class="bouton bouton-secondaire" href="index.php?action=auth">Se connecter</a>
        <?php endif; ?>
    </nav>
</header>

<section class="hero">
<div class="hero-contenu" style="margin: 0 auto; text-align: center; max-width: 750px; display: flex; flex-direction: column; align-items: center;">        <h1>Creez un CV qui vous ressemble en quelques minutes</h1>
        <p class="hero-description">
            Choisissez un modele elegant, remplissez vos informations et visualisez votre CV en temps reel.
            Telechargez-le ensuite au format PDF, pret a etre envoye aux recruteurs.
        </p>
        <a class="bouton bouton-principal bouton-large" href="index.php?action=auth">Generer un CV</a>
    </div>

</section>





<footer class="pied-page">
    
</footer>
</body>
</html>
