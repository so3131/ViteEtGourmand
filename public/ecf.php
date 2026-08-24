Compétences du référentiel concernées
Toutes les compétences du titre professionnel développeur Web et Web Mobile seront
analysées grâce à ce projet.
Activité – Type 1 : Développer la partie front-end d'une application web ou web mobile
sécurisée
è Installer et configurer son environnement de travail en fonction du projet web ou web
mobile
è Maquetter des interfaces utilisateur web ou web mobile
è Réaliser des interfaces utilisateur statiques web ou web mobile
è Développer la partie dynamique des interfaces utilisateur web ou web mobile
Activité – Type 2 : Développer la partie back-end d'une application web ou web mobile
sécurisée
è Mettre en place une base de données relationnelle
è Développer des composants d'accès aux données SQL et NoSQL
è Développer des composants métier coté serveur
è Documenter le déploiement d'une application dynamique web ou web mobile
Page 2 sur 12
©Studi Reproduction interdite
Présentation de l’entreprise
Présentation de l’entreprise
« Vite & Gourmand » est une entreprise constituée de deux personnes, Julie et José. Elle existe
depuis 25 ans à Bordeaux, et propose leurs prestations pour tout événement (simple repas
comme Noel ou encore Pâques) au travers d’un menu en constante évolution.
Les menus sont envoyés par mail aux habitués, mais Julie a eu l’idée d’obtenir une application
web permettant d’augmenter leurs visibilités ainsi que proposer leurs menus bien plus
facilement et à tous et toutes.
Le souci est qu’ils n’ont pas de notions sur l’informatique, de ce fait, ils ont choisi d’engager
une entreprise, FastDev, qui est réputée pour des développements de qualité et assez rapide.
Après l’obtention de votre diplôme, vous avez été embauché au sein de FastDev et missionné
sur ce projet.
Le but de l’application est de pouvoir montrer le menu aux visiteurs ainsi que permettre la
commande.
Description du projet
Vous êtes sous la direction du chef de projet, qui a collecté les besoins et vous les expose à
travers les besoins suivants :
Page d’accueil
La page d’accueil doit comporter :
Ø Présentation de l’entreprise
Ø Mise en avant du professionnalisme de l’équipe
Ø Les avis clients qui sont validés
Page 3 sur 12
©Studi Reproduction interdite
Menu de l’application
Le menu doit comporter au minimum :
Ø Retour vers la page d’accueil
Ø Accès à tous les menus
Ø Connexion (la connexion sera possible uniquement pour les employé, administrateur
et utilisateurs). Vous pouvez prendre la liberté de créer d’autres catégories que vous
jugez nécessaires, mais veillez à respecter les aspects réglementaires (RGPD et
sécurité).
Ø Accès à la page de contact
Pied de page
Les horaires doivent être visible sur le pied de page, du lundi au dimanche.
Un accès vers la page mention légales ainsi que les conditions générale de vente doivent être
proposé.
Vue globale de tous les menus
Une vue globale est nécessaire afin de proposer une interface simple et récapitulative de tous
les menus proposés par l’entreprise.
Ces éléments, doivent être configurable depuis l’espace “Administrateur” ainsi que
« Employé », un menu dispose des caractéristiques suivantes :
Ø Un titre
Ø Une galerie d’image
Ø Une description (présentation du menu)
Ø Thème (Noel, Pâques, classique, évènement)
Ø Une liste de plat possible (entrée, plat ainsi que dessert)
Ø Un nombre de personne minimale
Ø Le prix pour le nombre de personne minimale
Ø Chaque plat peut posséder une liste d’allergènes
Ø Les conditions de ce menu (par exemple, nécessité de commander ce menu x jours /
semaines avant la prestation ou encore des précautions de stockage)
Ø Un Regime (vegetarien, vegan, classique) : vous pouvez alimenter d’avantage cette
catégorie
Ø Une entrée ou un plat / dessert peuvent être présent dans plusieurs menus.
Ø Stock disponible (par exemple, il reste 5 commande possible de ce menu)
Page 4 sur 12
©Studi Reproduction interdite
La vue globale doit présenter le titre de chaque menu, ainsi qu’une description suivie du
nombre de personnes minimale et du prix associés, un bouton doit être disponible afin de
visualiser le détail du menu. Ce visuel doit être disponible pour les personnes non
authentifiées comme authentifié.
Des filtres doivent être disponibles sur la page globale afin de permettre une recherche rapide
d’un menu souhaité : ----
Filtre par prix maximum
Filtre sur une fourchette de prix
Filtre par thème
Filtre sur un régime
Filtre par nombre de personne minimum
Après avoir alimenté le filtre, il est nécessaire d’actualiser les menus affichés de manière
dynamique (sans rechargement de page).
Creation de compte
Un visiteur peut se créer un compte, pour cela, il devra communiquer des informations : ----
Nom ainsi que le prénom
Numéro de GSM
Adresse mail et postale
Mot de passe sécurisé (10 caractère minimum constitué au minima d’un caractère
spécial, une majuscule, une minuscule, un chiffre)
A la création du compte, il lui sera confié le role de “utilisateur”. Il recevra en réponse à son
inscription, un mail de bienvenu de manière automatique.
Connexion
Pour se connecter, il devra juste saisir son username (mail) suivi de son mot de passe.
Si le mot de passe est oublié, il pourra le réinitialiser via un bouton prévu à cet effet : un lien
par mail lui sera envoyé afin de l’inviter à le réinitialiser, pour cela, il devra juste donner son
mail dans le formulaire.
Vue détaillée d’un menu
Après clic sur le bouton d’un menu afin d’en visualiser le détail dans la vue Globale, il doit être
possible de visualiser tous les éléments des menus. Ainsi, toutes les informations énumérées
dans la base de données doivent être visible.
De plus, un bouton « commande » doit être présent afin de pouvoir commander le menu (au
clic, cela doit rediriger l’utilisateur (personne authentifié) sur l’espace de commande avec le
menu de commande pré-rempli).
Page 5 sur 12
©Studi Reproduction interdite
Si la personne est un visiteur (personne non authentifié) alors il lui sera demander de se
connecter ou de concevoir un compte avant d’accéder à la page de commande.
Attention : il doit être mis bien en évidence les conditions de ce menu, afin d’éviter que le
client puisse se plaindre qu’il n’a pas vu l’information.
Page 6 sur 12
©Studi Reproduction interdite
Commande d’un menu
Il est possible de commander un menu, en cliquant, sur le bouton de commande depuis la vue
détaillée d’un menu.
Sur cette page, il sera demandé les informations de la prestation : -
Nom, mail et prénom du client (auto remplie depuis les informations du
compte) - - -
Adresse et date de la prestation (facturation de 5 euros (majoré de 59 centimes
par kilomètre parcouru) si la livraison n’est pas dans la ville de bordeaux).
Heure souhaitée de livraison ainsi que la date, suivi du lieu
GSM du client (auto remplie depuis les informations du compte)
L’étape suivante, consiste à demander le menu choisi (si le client vient sur la page après avoir
cliqué sur le bouton « commander » d’un menu, alors, le menu est automatiquement déjà
positionner).
Enfin, il choisit le nombre de personne. Le prix est mis à jour, mais, il y a l’obligation de
commander pour le nombre minimum de personne inscrit dans le menu.
Julie vous indique qu’une réduction de 10% est appliquée pour toutes commandes ayant 5
personnes de plus que le nombre de personnes minimum indiqué dans le menu.
Une vue détaillée du prix est visible avant validation (prix menu ainsi que le prix de la livraison).
Après avoir commandé un menu, le visiteur va recevoir un mail lui confirmant la commande.
Espace Utilisateur
Un utilisateur, depuis son espace peut :
Visualiser l’ensemble des commandes dans le détail qu’il a effectué ainsi que modifier ses
informations personnelles.
L’annulation de commande est possible, tant qu’un employé n’a pas passé la commande en
“accepté”, la modification est également possible (tout est modifiable, sauf, le choix du
menu).
Une fois que la commande est “accepté”, l’utilisateur a accès au suivi de sa commande.
Le suivi de la commande énumère tous les états de sa commande suivi de la date et l’heure
de modification.
Quand la commande est “terminée”, alors, l’utilisateur est notifié par mail qu’il peut se
connecter à son compte pour donner son avis depuis la commande.
Il doit pouvoir donner entre note entre 1 et 5, suivi d’un commentaire.
Page 7 sur 12
©Studi Reproduction interdite
Espace Employé
Un employé, après connexion, peut accéder à son espace :
Il peut modifier / supprimer les menus, plats, et les horaires.
Cependant, il ne peut pas modifier / annuler les commandes avant d’avoir contacté le client
par appel GSM ou mail. (Il devra mettre un motif d’annulation en spécifiant le mode de contact
ainsi que le motif).
Un filtre sur les commandes doit être disponible afin de vite retrouver les commandes à
effectuer (par statut) ou d’un client en particulier. Ce filtre est aussi visible dans l’espace
employé.
La mise à jour des commandes est possible : - - - - - -
“accepté”: lorsque la commande reçue est validée par l’équipe
“en préparation” : la commande est en cours de préparation par l’équipe cuisine
“en cours de livraison” : la commande est en cours de livraison par l’équipe logistique
de Julie
“livré” : l’équipe livraison a livré le client
“en attente du retour de matériel” : si du matériel a été prêté au client. Il doit le
restituer, dès que ce statut est atteint, le client reçoit un mail lui notifiant que si sous
10 jours ouvré, le matériel n’est pas restitué, alors, il devra s’acquitter de 600 euros de
frais (mentionné dans les conditions générales de vente). Pour rendre le matériel, le
client devra répondre prendre contact avec la société.
“terminée” : soit quand la commande est livrée sans prêt de matériel, soit quand le
matériel a été restitué.
L’employé peut également, valider les avis reçus par les utilisateurs afin qu’ils soient visibles
sur la page d’accueil. Il peut également en refuser.
Page 8 sur 12
©Studi Reproduction interdite
Espace Administrateur
L’administrateur, après connexion, peut accéder à son espace. Il peut créer un compte de type
“employe”, il doit pour cela, fournir un email (qui sera l’username) ainsi qu’un mot de passe.
L’employe en question, va recevoir un mail lui notifiant qu'un compte, pour lui, a été crée,
cependant, le mot de passe n’est pas communiqué dans le mail. Il devra se rapprocher de
l’administrateur afin de l’obtenir.
Il doit être possible également de rendre inutilisable un compte employé en cas de départ de
l’entreprise par exemple.
José précise que vous devez lui créer ce compte et qu’il ne doit pas être possible de créer un
compte Administrateur depuis l’application.
Il doit en plus, être capable de faire tout ce qu’un employé peut faire depuis son espace.
Enfin, il doit pouvoir visualiser depuis son espace le nombre de commande par menu et
pouvoir les comparer entre eux via un graphique. (Les données doivent venir d’une base de
données non relationnelle).
Un calcul de chiffre d'affaires par menu doit être disponible avec des filtres par menu, ainsi
que, sur une durée.
Contact
Un visiteur peut contacter l’entreprise s’il le souhaite, pour cela, il devra accéder à la page
contact depuis le menu applicatif. À la suite de cela, il aura accès à un formulaire qui va lui
demander un titre, une description ainsi que son mail afin qu’il puisse obtenir une réponse.
Pour donner suite à cet envoie, la demande est envoyée par mail à l’entreprise.
Le chef de projet conclut que le client exige le déploiement de l'application et que des
Pénalités seront appliquées si l'application n'est pas en ligne et fonctionnelle au moment de
La livraison.
De plus, vous devrez rendre l’application accessible, conformément au RGAA.
Page 9 sur 12
©Studi Reproduction interdite
Annexe 1 : schémas de base de données relationnelle (MCD)
Stack technique
Aucune technologie n’est obligatoire pour cet ECF, à l’exception de l’utilisation d’une base de
données relationnelle et non relationnelle.
Voici un exemple de stack technique possible :
o Front : HTML 5, CSS (Bootstrap), JS
o Back-end : PhP avec utilisation de PDO
o Base de données relationnelle : MySQL , MariaDB ou PostGreSQL
o Base de données NoSQL : MongoDB
o Déploiement : fly.io , Heroku , Azure , vercel
Page 10 sur 12
©Studi Reproduction interdite
Objectif & Livrables
L’objectif de cet ECF est que vous passiez sur chaque étape nécessaire à l’élaboration d’une
solution de qualité : analyse des besoins, maquettage, intégration, développement des règles
de gestion ainsi que le déploiement.
Vous devrez détailler dans votre copie les dispositions que vous avez prises notamment sur la
sécurité. De plus, vous devrez justifier tous vos choix techniques : il est très important de
pouvoir les justifier et exposer votre point de vue sur différentes technologies.
Les livrables de ce sujet, sont les suivants :
è Le lien du (ou des) dépôt(s) github PUBLIC où sera présent le code de votre application
è Le lien de votre / vos applications déployés
è Le lien vers votre logiciel de gestion de projet (Jira , Notion, Trello , etc)
Votre git, devra comporter :
o Un fichier README.md contenant la démarche à suivre afin de déployer votre
application en local
o Les bonnes pratiques de git doivent être appliqué
§ Une branche principale
§ Une branche de développement
• Chaque fonctionnalité sera une branche issue de la branche
développement, après test, le merge sera effectué vers la
branche développement
• Une fois que la branche développement est correctement
testée, il faudra effectuer un merge vers la branche principale
o Les fichiers de création de base de données mais également d’intégration de
données (cela doit être un fichier SQL). (L’utilisation de fixture et / ou de
migration n’implique pas que vous maitrisez le SQL)
o Manuel d’utilisation en format pdf
§ Il doit présenter l’application et donner des identifiants afin de réaliser
les différents parcours possibles.
o Une charte graphique en format pdf
§ Palette de couleurs utilisé ainsi que la police
§ L’export des maquettes attendues (wireframes & mockup) (3
maquettes bureautiques et 3 maquettes mobiles)
Page 11 sur 12
©Studi Reproduction interdite
o Une documentation traitant de votre gestion de projet
§ Explication de votre gestion de projet
o Une documentation technique de votre application
§ Réflexions initiale technologique sur le sujet
§ Configuration de votre environnement de travail
§ Modèle conceptuel de données (ou diagramme de classe)
§ Diagramme d’utilisation ainsi que le diagramme de séquence
§ Documentation du déploiement de votre application expliquant votre
démarche ainsi que les différentes étapes.
Page 12 sur 12