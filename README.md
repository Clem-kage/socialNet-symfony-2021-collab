# Développement avec un framework Symfony



## L'application

Nous allons développer un mini réseau social, permettant aux membres de publier des messages sur un "mur".
Les membres pourront s'ajouter en ami, et la visibilité des posts sur le mur changera en fonction du visiteur. Si le visiteur est ami avec l'auteur, il verra tous les posts, sinon il verra seulement les posts publics.

## RoadMap :


Afin de gagner le maximum de temps, nous allons tirer parti au maximum des possibilités offertes par le framework et les librairies qui gravitent autour.


1. Prototype 

    - [ ] Concevoir l'entité Post [https://symfony.com/doc/current/doctrine.html#creating-an-entity-class](https://symfony.com/doc/current/doctrine.html#creating-an-entity-class)
    - [ ] Ajouter des fixtures [https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html](https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html)
    - [ ] Afficher la page de liste des posts (Créer une nouvelle classe `PostController`) 
    - [ ] Afficher la page de fomulaire de création/édition d'un post



2. **Page de profil**

      Pouvoir voir les informations d'une personne + la liste des `Posts` dont il est l'auteur

      - [x] Nouvelle route, _avec le nom de la personne dans l'URL_
      - [x] Nouveau template ("nouvelle page") qui affiche le nom de la personne
      - [ ] Améliorer la route, pour vérifier que la personne existe en base de donnée
      - [X] Améliorer la route, pour récupérer les `Post` de la personne
      - [x] Améliorer le template pour afficher les `Posts` 
   
      - [ ] Ajouter des liens de navigation interne pour acccéder aux pages profils des utilisateurs
            - Liste des auteurs
            - Sur le nom de l'auteur de chaque `Post`


3. Mise a jour d'un `Post`

   1. Ajouter une Route permettant de modifier un `Post`
      - Récupérer le contenu du `Post` pour pouvoir le modifier
      - Afficher le contenu de ce post dans un Form
      
   2. le client va modifier le formulaire, en renvoyer les nouvelles données
      
   3. - valider les données (a developper)
      - utiliser le manager de Doctrine pour enregistrer les modifs
   
   ? ajouter des liens pour atteindre cette nouvelle Route



















2. Authentification
    - [ ] Concevoir l'entité User
    - [ ] Créer les routes pour pouvoir s'incrire, se connecter, et se déconnecter
    - [ ] Protéger les pages de l'application, en ne les affichant qu'aux utilisateurs connectés


3. Visibilité des `Posts`
    - [ ] Définir la relation entre les entités `User` et `Post`
    - [ ] Afficher la page de liste des posts d'un utilisateur spécifique
    - [ ] Afficher uniquement les posts publics sur la page d'accueil


4. Systeme de follower
    - [ ] Définir la relation entre les `User`
    - [ ] Mettre en place tout le systeme pour pouvoir "follow" un autre utilisateur
    - [ ] Afficher les posts privés uniquement aux followers

5. Photo de profil
    - [ ] Utiliser la librairie GD pour générer une photo de profil aleatoire a chaque utilisateur
    - [ ] Permettre aux utilisateurs d'uploader leur propre photo de profil

6. Like des Posts
    - [ ] Définir la relation en Post et User
    - [ ] Afficher sur un button sur chaque Post pour pouvoir le liker / unliker
    - [ ] Un auteur ne pas liker son propre Post
    (- [ ] Seuls les followers peuvent liker un Post privé)
    - [ ] Ajouter une couche de JS pour liker/unliker sans recharger la page
