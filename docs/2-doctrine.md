# Travailler avec Doctrine

**[Documentation de Symfony](https://symfony.com/doc/current/doctrine.html) **

# Les entités

## Introduction

Dans la majorité des applications, nous sommes amenés à manipuler des données.

Par exemple, si on développe un blog, on aura des articles et des commentaires, et si on développe une plateforme bancaire, on aura des comptes, des transactions, et des crédits...
Le rôle du développeur est de "représenter" ces données du point de vue du code, de manière à pouvoir travailler avec.

Les données peuvent être de type très variés, et de plus, leur forme dépend souvent du type de l'application qu'on développe. Un utilisateur sera représenté différement (c'est à dire aura des propriétés et des méthodes différentes) sur une application de type réseau social (liste d'amis, nombres de likes, ...) que sur une application e-commerce (produits favoris, historiques des commandes, ...)

Ces données représentent donc **un besoin ou une contrainte métier** très forte (un `Article` pour un blog est sans doute l'objet le plus important) et sont souvent le point central de notre application. En général, dans la programmation orientée objet, on utilise une **classe** pour représenter ce type de donnée, car de nombreuses méthodes seront utilisées pour garantir l'intégrité de ces contraintes métiers. (Rappel : Avec la POO, on cherche à encapsuler les choses pour gagner en flexibilité)

> Dans le projet précédent, nous avons appelé ces objets des _modèles_.

Avec Symfony, on parlera plutôt de **couche modèle**, car le nombres de classes qui interviennent dans le processus est bien plus élevé. Les classes de bases qui représentent nos données sont appelées des **entités**. Elles sont toutes rangées dans le dossier `src/Entity`.

## Créer / modifier une entité

### Ecrire le code PHP

#### Créer une entité

Utiliser la commande

```sh
php bin/console make:entity
```

Un utilitaire va alors poser des questions et nous guider pendant la création de notre entité.
Le code est généré au fur et à mesure que l'on répond aux questions.

#### Ajouter des propriétés à une entité

On peut utiliser la même commande pour ajouter de nouvelles propriétés à une entité existante. L'utilitaire se chargera de mettre à jour le fichier php sans tout écraser.

#### Supprimer une propriété

On ne peut pas utiliser cette commande pour supprimer une propriété. Il faut la supprimer "à la main" dans le code. Ne pas oublier de supprimer également toutes les méthodes associées (getter/setter, ...)

#### Mettre à jour une propriété

On ne peut pas utiliser cette commande pour mettre à jour une propriété, par exemple pour changer son type. Le plus simple est de supprimer la propriété, puis de la recréer avec la commande.

> Tout le code généré par Symfony reste modifiable à volonté. Il ne faut pas hésiter à rajouter des méthodes, ou à modifier / supprimer celles qui existent.

### Mettre à jour la base de données

Une fois la création / mise à jour terminée, l'entité est prête pour la partie PHP de l'application. Il faut ensuite informer la base de données que la structure a changé, il faut donc faire une [migration](#migrations).

```sh
php bin/console make:migration
```

> Pour ne pas avoir de problème, il faut mieux générer la migration une fois que toutes les modifications sur les entités ont été faites.

```sh
# On ajoute deux entités à l'application
php bin/console make:entity User
php bin/console make:entity Product

# On créé UNE SEULE migration
php bin/console make:migration
```

<h1 id="migrations">Les migrations</h1>

Il est commun pour un projet PHP de dépendre d'une base de données. Dans le cadre de cette formation nous nous appuyons sur le logiciel MySQL, un système de base de données relationnelle. Comme on a pu le constater dans les projets précédents, **les contraintes** dans un système MySQL peuvent être **très fortes** et c'est d'ailleurs ce qui en fait sa **principale utilité**. Par exemple, on peut avoir des containtes sur un champ particulier (_typage_, ...), sur un groupe de champ (_clé composite_), sur une table (_contrainte d'unicité_, ...), ou entre plusieurs tables (_clé étrangère_, ...).

Il est difficile et même souvent illusoire de prévoir toutes ces contraintes lors de la conception initiale de la base de données, et il arrive fréquemment que l'on doive faire des **mises à jour de sa structure pendant le développement**. En règle générale, la modifiation du schéma de la BDD va de pair avec la modification du code source.

Or, si Git nous permet d'avoir un historique très détaillé de l'évolution du code source, la **modification de la base de données** à l'inverse est généralement **définitive** : une fois que l'on a modifié son état, la base de données "oublie" son état précédent. Cela peut poser de **nombreux problèmes**, notament si on travaille à plusieurs. Par exemple, le développeur A fait une modification du code exigeant une modification de la BDD, et pousse son code sur la branche `master`. Le développeur B récupère la branche `master` qui ne correspond plus à la structure de sa base de données, et il ne peut donc plus travailler.
Le problème peut également exister entre plusieurs serveurs (tests, staging, production...)

Les migrations sont un méchanisme permettant de pallier à ce problème, justement en ré **intégrant une notion d'historique** dans la gestion de la **structure de la base de données**. Ce méchanisme n'est pas propre à Doctrine ni à Symfony, mais ces deux outils combinés nous fournissent un moyen de gérer ces migrations facilement.

### Fonctionnement général

L'idée principale est qu' **une modification de la structure de la base de données doit être versionnée**, au même titre que doit l'être toute modification du code source. 

> _On parle bien ici de la **structure** de la base, pas des données qu'elle contient_

Chaque modification de la base de données sera donc enregistrées dans un fichier spécifique, qu'on appele une **migration**.
Chaque migration est une classe PHP contenant deux méthodes :

- `up` : la méthode qui contient toutes les instructions pour mettre en place la modification
- `down` : la méthode qui permet de "défaire" la modification (_rollback_)
- `getDescription` : une méthode qui résume ce que faite cette migration (comme un message de commit dans `Git`)

Dans Symfony, ces fichiers sont stockés dans le dossiers `Migrations`, et sont automatiquements nommés avec la date du moment où la migration a été créée.
Cela va permettre à Doctrine d'éxecuter la pile de migrations dans l'ordre chronologique, et également au dévelopeurs de s'y retrouver un peu.

Pour savoir quoi mettre dans les méthodes `up` et `down`, Doctrine va comparer la structure actuelle de la base de données (_MySQL_) avec celle de vos entités (_PHP_), puis générer automatiquement les requêtes SQL pour **faire correspondre la structure de la base avec celle des entités**.

> Avec Doctrine, il ne faut **jamais** modifier la base de données "à la main" (dans PHPMyAdmin par exemple), sinon tout le système devient inutilisable.

### Créer une migration

#### Comment créer une migration ?

Dans la console, on utilise la commande :

```sh
php bin/console make:migration
```

Il est conseillé d'ajouter la description de la migration dans la méthode dédiée (`getDescription`), afin de s'y retrouver plus tard, car la liste des migrations peut devenir longue !

#### Quand créer une migration ?

**Il est nécessaire de créer une migration lorsqu'on a modifié une entité**, c'est à dire ajouté, modifié, ou supprimé un ou plusieurs champs.

En revanche, il est inutile (et même contre productif) de créer une migration pour chaque modification de champs, il vaut mieux modifier l'ensemble l'entité (ou des entités), puis quand c'est terminé, générer la migration.

###### Exemple

Modifier l'entité autant de fois que nécessaire 

```php

class User {

  private $email;
  
  private $password;

  {+ additions +} private $firstName; 
 
  {+ additions +} private $lastName;
}
```

Puis créer la migration 

```sh
php bin/console make:migration
```

### Exécuter les migrations

Quand on crée une migration, on crée seulement des fichiers PHP, **la base de données n'est pas modifiée**.

Pour altérer le schéma de la base, il faut **exécuter** les migrations :

```sh
php bin/console doctrine:migrations:migrate
```

A ce moment là, doctrine va aller vérifier dans la base de données la dernière migration déjà executée (grâce à la table `migration_versions`), puis executer toutes les migrations manquantes, dans l'ordre chronologique. (_Si aucune migration n'a été effectuée jusque là, l'intégralité des migrations sera joué_).


### Au secours ! Y a rien qui marche...

Au début, ce système de migrations peut sembler fastidieux, voire carrément frustrant quand il n'en fait qu'à sa tête. Pourtant, il faut garder en mémoire que **c'est lui qui a raison**, et que ce que l'on essaye de le forcer à faire est probablement illogique.


#### 1 - Vérifier ses migrations

La plupart des erreurs lors des migrations sont des erreurs de type _duplicate_ (table déja existante, colonne déjà existante, index déja existant)

La première chose à faire, c'est de **vérifier les fichiers de migration**. 

Lire les fonctions `up`, une par unes, dans l'ordre chronologiques. Même si vous ne comprenez pas toutes les reqûetes SQL, vous pouvez comprendre l'intention générale de la migration (surtout si vous avez défini sa description !)

Est ce que cet ordre est logique ? Y a t'il des opérations dupliquées ?

**Il est toujours possible de modifier une migration**, par exemple pour
supprimer des requêtes qui font doublon avec d'autres migrations (`CREATE TABLE ...` par exemple), ce qui peut arriver quand on a fait des `make:migration` successifs sans mettre à jour la base de données.

> Si on modifie quelque chose dans la fonction `up`, il faut impérativement modifier l'instruction contraire dans la fonction `down`.

En revanche il est **fortement déconseillé de modifier les requêtes SQL** elles mêmes, car la probabilité d'erreur est forte.




#### 2 - Supprimer la base de données (_méthode de bourrin mais efficace!_)

Une autre source de problèmes vient du faire que parfois, une migration comprenant une série d'instructions SQL génère une erreur en cours de séquence. Il est alors impossible de relancer les migrations, car on passe alors dans le cas cité au dessus. (_duplicate_)

##### Exemple : 

Premier essai...

```sql 
/* Success */
CREATE TABLE user

/* Erreur car une ligne d'un post  contient un updated_at à null */
ALTER TABLE post SET COLUMN updated_at NOT NULL
```

... Deuxième essai ...

```sql 
/* Erreur, car la table user à déjà eté crée !!! */
CREATE TABLE user

/* La migration ne va même plus jusque là */
/* ALTER TABLE post SET COLUMN updated_at NOT NULL */
```

... Et donc on est bloqué.

On peut essayer de revenir sur la dernière migration, avec la commande

```
php bin/console doctrine:migrations:migrate prev
```


Et si jamais, rien ne fonctionne, on peut "dropper" l'intégralité des tables de la bases de données (même la table `migration_versions`) pour rejouer toutes les migrations dans le bon ordre.

#### 3 - Supprimer la base et les migrations

Quand plus rien ne fonctionne, qu'on s'est bien énervé, qu'on a changé d'avis 36000 fois, et que décidément Doctrine c'est de la m...

On peut supprimer l'intégralité du contenu des migrations, de la base de données, puis recréer une seule migration. De cette manière, l'intégralit"é de la base sera regénérée en une seule commande.

```sh
# 1 - 'Drop' la base de données
php bin/console doctrine:database:drop --force

# 2 - Recréer la base de données
php bin/console doctrine:database:create

# 3 - Supprimer toutes les migrations
rm -rf Migrations

# 4 - Regénérer une migration
php bin/console make:migration

# 5 - Executer la migration
php bin/console doctrine:migrations:migrate
```



> A n'utiliser qu'en dernier recours, car cela détruit complètement l'intérêt des migrations puisqu'on perd tout l'historique. Si on revient en arrière dans le code avec Git, on va avoir de gros problèmes avec la base de données...


<h1 id="commands">Commandes utiles pour manipuler la base de donées</h1>

Créer la base de données :

```sh 
php bin/console doctrine:database:create
```

Executer les migrations (c.a.d mettre la base de données à jour par rapport au code PHP): 
```sh 
php bin/console doctrine:migrations:migrate
```

Charger les fixtures (first time):
```sh 
php bin/console doctrine:fixtures:load
```

Reload les fixtures :
```sh 
php bin/console doctrine:schema:drop --force && php bin/console doctrine:schema:update --force && php bin/console doctrine:fixtures:load -n
```

Détruire la base de données :

```sh 
php bin/console doctrine:database:drop --force
```
