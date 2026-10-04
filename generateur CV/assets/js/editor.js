document.addEventListener('DOMContentLoaded', function () {
    var formulaire = document.getElementById('formulaire-cv');
    var champTitre = document.getElementById('champ-titre');
    var listeExperiences = document.getElementById('liste-experiences');
    var listeFormations = document.getElementById('liste-formations');
    var apercuExperiences = document.getElementById('apercu-experiences');
    var apercuFormations = document.getElementById('apercu-formations');
    var apercuCompetences = document.getElementById('apercu-competences');
    var apercuLangues = document.getElementById('apercu-langues');
    var indicateur = document.getElementById('indicateur-sauvegarde');
    var boutonEnregistrer = document.getElementById('bouton-enregistrer');
    var boutonPdf = document.getElementById('bouton-pdf');
    var champPhoto = document.getElementById('champ-photo');
    var champSuppressionPhoto = document.getElementById('supprimer-photo');
    var boutonRetirerPhoto = document.getElementById('bouton-retirer-photo');
    var apercuPhoto = document.getElementById('apercu-photo');
    var apercuCv = document.getElementById('apercu-cv');
    var tailleMaxPhoto = 2097152;
    var modifie = false;

    var valeursParDefaut = {
        prenom: 'Prenom',
        nom: 'Nom',
        poste_recherche: 'Poste recherche',
        email: 'email@gmail.com',
        telephone: '0600000000',
        adresse: 'Ville, Pays'
    };

    function synchroniserChamp(champ) {
        var cle = champ.dataset.champ;
        var cibles = document.querySelectorAll('[data-apercu="' + cle + '"]');
        var valeur = champ.value.trim();
        cibles.forEach(function (cible) {
            if (valeur === '') {
                cible.textContent = valeursParDefaut[cle] !== undefined ? valeursParDefaut[cle] : '';
                cible.classList.add('cv-valeur-vide');
            } else {
                cible.textContent = valeur;
                cible.classList.remove('cv-valeur-vide');
            }
        });
    }

    function synchroniserListeSimple(champ, conteneur) {
        conteneur.innerHTML = '';
        champ.value.split('\n').forEach(function (ligne) {
            var texte = ligne.trim();
            if (texte === '') {
                return;
            }
            var element = document.createElement('li');
            element.textContent = texte;
            conteneur.appendChild(element);
        });
    }

    function creerChamp(libelle, nom, valeur, placeholder, typeLong) {
        var bloc = document.createElement('div');
        bloc.className = 'champ';
        var etiquette = document.createElement('label');
        etiquette.textContent = libelle;
        var saisie = typeLong ? document.createElement('textarea') : document.createElement('input');
        if (typeLong) {
            saisie.rows = 3;
        } else {
            saisie.type = 'text';
        }
        saisie.dataset.cle = nom;
        saisie.placeholder = placeholder;
        saisie.value = valeur || '';
        bloc.appendChild(etiquette);
        bloc.appendChild(saisie);
        return bloc;
    }

    function ajouterEntree(conteneur, type, donnees) {
        donnees = donnees || {};
        var entree = document.createElement('div');
        entree.className = 'entree-dynamique';

        var enTete = document.createElement('div');
        enTete.className = 'entete-entree';
        var titre = document.createElement('span');
        titre.textContent = type === 'experience' ? 'Experience' : 'Formation';
        var suppression = document.createElement('button');
        suppression.type = 'button';
        suppression.className = 'bouton bouton-petit bouton-danger';
        suppression.textContent = 'Supprimer';
        suppression.addEventListener('click', function () {
            entree.remove();
            modifie = true;
            rafraichirEntrees();
        });
        enTete.appendChild(titre);
        enTete.appendChild(suppression);
        entree.appendChild(enTete);

        if (type === 'experience') {
            entree.appendChild(creerChamp('Poste occupe', 'poste', donnees.poste, 'Developpeuse web', false));
            entree.appendChild(creerChamp('Entreprise', 'entreprise', donnees.entreprise, 'Societe Ahlam', false));
            entree.appendChild(creerChamp('Periode', 'periode', donnees.periode, 'Janvier 2022 - Aujourd\'hui', false));
            entree.appendChild(creerChamp('Description des missions', 'description', donnees.description, 'Developpement des modules internes', true));
        } else {
            entree.appendChild(creerChamp('Diplome', 'diplome', donnees.diplome, 'Licence informatique', false));
            entree.appendChild(creerChamp('Etablissement', 'etablissement', donnees.etablissement, 'Ecole Superieure de technologie Nador', false));
            entree.appendChild(creerChamp('Periode', 'periode', donnees.periode, '2019 - 2022', false));
            entree.appendChild(creerChamp('Details', 'description', donnees.description, 'Mention bien, specialite developpement', true));
        }

        entree.querySelectorAll('input, textarea').forEach(function (saisie) {
            saisie.addEventListener('input', rafraichirEntrees);
        });

        conteneur.appendChild(entree);
        rafraichirEntrees();
    }

    function collecterEntrees(conteneur) {
        var resultats = [];
        conteneur.querySelectorAll('.entree-dynamique').forEach(function (entree) {
            var objet = {};
            var rempli = false;
            entree.querySelectorAll('[data-cle]').forEach(function (saisie) {
                objet[saisie.dataset.cle] = saisie.value.trim();
                if (saisie.value.trim() !== '') {
                    rempli = true;
                }
            });
            if (rempli) {
                resultats.push(objet);
            }
        });
        return resultats;
    }

    function rendreEntrees(liste, conteneur, type) {
        conteneur.innerHTML = '';
        liste.forEach(function (element) {
            var bloc = document.createElement('div');
            bloc.className = 'cv-entree';

            var ligneTitre = document.createElement('div');
            ligneTitre.className = 'cv-entree-titre';
            var principal = document.createElement('strong');
            principal.textContent = type === 'experience' ? element.poste : element.diplome;
            var periode = document.createElement('span');
            periode.className = 'cv-periode';
            periode.textContent = element.periode || '';
            ligneTitre.appendChild(principal);
            ligneTitre.appendChild(periode);
            bloc.appendChild(ligneTitre);

            var secondaire = document.createElement('div');
            secondaire.className = 'cv-entree-sous-titre';
            secondaire.textContent = type === 'experience' ? element.entreprise : element.etablissement;
            bloc.appendChild(secondaire);

            if (element.description) {
                var description = document.createElement('p');
                description.className = 'cv-entree-description';
                description.textContent = element.description;
                bloc.appendChild(description);
            }

            conteneur.appendChild(bloc);
        });
    }

    function rafraichirEntrees() {
        rendreEntrees(collecterEntrees(listeExperiences), apercuExperiences, 'experience');
        rendreEntrees(collecterEntrees(listeFormations), apercuFormations, 'formation');
    }

    function afficherMessage(texte, estErreur) {
        indicateur.textContent = texte;
        indicateur.className = 'indicateur-sauvegarde ' + (estErreur ? 'message-erreur' : 'message-succes');
        window.setTimeout(function () {
            indicateur.textContent = '';
            indicateur.className = 'indicateur-sauvegarde';
        }, 4000);
    }

    function enregistrer() {
        var donnees = new FormData();
        donnees.append('id_cv', document.getElementById('id-cv').value);
        donnees.append('id_template', document.getElementById('id-template').value);
        donnees.append('titre', champTitre.value);
        formulaire.querySelectorAll('[data-champ]').forEach(function (champ) {
            donnees.append(champ.dataset.champ, champ.value);
        });
        donnees.append('experiences', JSON.stringify(collecterEntrees(listeExperiences)));
        donnees.append('formations', JSON.stringify(collecterEntrees(listeFormations)));
        if (champPhoto && champPhoto.files.length > 0) {
            donnees.append('photo', champPhoto.files[0]);
        }
        if (champSuppressionPhoto) {
            donnees.append('supprimer_photo', champSuppressionPhoto.value);
        }

        boutonEnregistrer.disabled = true;
        boutonEnregistrer.textContent = 'Enregistrement...';

        fetch('index.php?action=enregistrer', {
            method: 'POST',
            body: donnees
        }).then(function (reponse) {
            return reponse.json();
        }).then(function (resultat) {
            afficherMessage(resultat.message, !resultat.succes);
            if (!resultat.succes) {
                return;
            }
            modifie = false;
            document.getElementById('id-cv').value = resultat.id_cv;
            if (champPhoto) {
                champPhoto.value = '';
                champSuppressionPhoto.value = '0';
            }
            if (resultat.photo && apercuPhoto) {
                apercuPhoto.src = resultat.photo + '?v=' + Date.now();
            }
        }).catch(function () {
            afficherMessage("Le serveur a repondu de facon inattendue. Le CV n'a pas ete enregistre.", true);
        }).then(function () {
            boutonEnregistrer.disabled = false;
            boutonEnregistrer.textContent = 'Enregistrer';
        });
    }

    function telechargerPdf() {
        var nomFichier = (champTitre.value.trim() || 'cv').replace(/[\\/:*?"<>|]+/g, '-') + '.pdf';
        var copie = apercuCv.cloneNode(true);

        copie.removeAttribute('id');
        copie.style.boxShadow = 'none';
        copie.style.margin = '0';

        boutonPdf.disabled = true;
        boutonPdf.textContent = 'Generation...';

        var generation = html2pdf().set({
            margin: 0,
            filename: nomFichier,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, scrollX: 0, scrollY: 0 },
            jsPDF: { unit: 'px', format: [794, 1124], orientation: 'portrait', hotfixes: ['px_scaling'] },
            pagebreak: { mode: ['css'], avoid: '.cv-entree' }
        }).from(copie).save();

        Promise.resolve(generation).catch(function () {
            afficherMessage('La generation du PDF a echoue.', true);
        }).then(function () {
            boutonPdf.disabled = false;
            boutonPdf.textContent = 'Telecharger PDF';
        });
    }

    formulaire.querySelectorAll('[data-champ]').forEach(function (champ) {
        var cle = champ.dataset.champ;
        if (cle === 'competences') {
            champ.addEventListener('input', function () {
                synchroniserListeSimple(champ, apercuCompetences);
            });
            synchroniserListeSimple(champ, apercuCompetences);
            return;
        }
        if (cle === 'langues') {
            champ.addEventListener('input', function () {
                synchroniserListeSimple(champ, apercuLangues);
            });
            synchroniserListeSimple(champ, apercuLangues);
            return;
        }
        champ.addEventListener('input', function () {
            if (cle === 'telephone') {
                champ.value = champ.value.replace(/\D/g, '');
            }
            synchroniserChamp(champ);
        });
        synchroniserChamp(champ);
    });

    document.getElementById('ajouter-experience').addEventListener('click', function () {
        ajouterEntree(listeExperiences, 'experience', {});
    });

    document.getElementById('ajouter-formation').addEventListener('click', function () {
        ajouterEntree(listeFormations, 'formation', {});
    });

    if (champPhoto) {
        champPhoto.addEventListener('change', function () {
            if (champPhoto.files.length === 0) {
                return;
            }
            if (champPhoto.files[0].size > tailleMaxPhoto) {
                champPhoto.value = '';
                afficherMessage('La photo ne doit pas depasser 2 Mo.', true);
                return;
            }
            var lecteur = new FileReader();
            lecteur.onload = function (evenement) {
                apercuPhoto.src = evenement.target.result;
            };
            lecteur.readAsDataURL(champPhoto.files[0]);
            champSuppressionPhoto.value = '0';
        });
    }

    if (boutonRetirerPhoto) {
        boutonRetirerPhoto.addEventListener('click', function () {
            champPhoto.value = '';
            apercuPhoto.src = 'assets/img/photo-placeholder.svg';
            champSuppressionPhoto.value = '1';
            modifie = true;
        });
    }

    boutonEnregistrer.addEventListener('click', enregistrer);

    boutonPdf.addEventListener('click', telechargerPdf);

    formulaire.addEventListener('input', function () {
        modifie = true;
    });

    champTitre.addEventListener('input', function () {
        modifie = true;
    });

    window.addEventListener('beforeunload', function (evenement) {
        if (modifie) {
            evenement.preventDefault();
            evenement.returnValue = '';
        }
    });

    if (window.donneesInitiales.experiences.length > 0) {
        window.donneesInitiales.experiences.forEach(function (element) {
            ajouterEntree(listeExperiences, 'experience', element);
        });
    } else {
        ajouterEntree(listeExperiences, 'experience', {});
    }

    if (window.donneesInitiales.formations.length > 0) {
        window.donneesInitiales.formations.forEach(function (element) {
            ajouterEntree(listeFormations, 'formation', element);
        });
    } else {
        ajouterEntree(listeFormations, 'formation', {});
    }

    rafraichirEntrees();
    modifie = false;
});
