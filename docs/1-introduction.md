# Introduction a Symfony


## Le concept de "Route"

Une route c'est l'association  d'une **URL** et d'une **méthode** d'un controller

Pour être valides, les méthodes du controller associées à une route doivent retourner une `Response`. Cette classe représente la réponse du serveur.

> Attention, il y a plusieurs classes `Response` dans le framework, il faut choisir `Symfony\Component\HttpFoundation\Response` 


Généralement, on a envie d'afficher des choses dans la réponse, mais parfois on voudra plutôt rediriger vers une autre adresse, ou même ne rien faire. Quoiqu'il arrive, le controller doit retourner une réponse.


## Rendre un template 

Pour afficher les données, on utilise **Twig**, un autre moteur de template que PHP.

L'idée de base, c'est que le **contrôleur** s'occupe de manipuler toutes les données, et quand il a fini son travail, il envoie ses données dans la **vue**, c'est-à-dire dans le template twig. 

> On dit qu'on _rend_ la vue.

On utilise pour cela la méthode `render` d'un Controller Symfony.

```php
    return $this->render("homepage.html.twig");
```

#### Exercice :

Créer une page statique "A propos", avec un template dédié. Cette page doit être accessible à l'adresse `/about`.

### Utiliser l'héritage des templates

Twig nous donne deux moyens très efficaces d'éviter de dupliquer du code :

- `include`: comme en PHP, cette directive sert à include des portions de HTML au sein d'autres fichiers.

- `extends`: Permet à un template "enfant" d'hériter d'un template "parent". Le template parent définit des emplacements (vides ou pré-remplis) , et le template enfat se charge de remplir les emplacements avec le contenu qui lui est propre. 

Ces **deux moyens sont complémentaires**, et en les utilisant à bon escient on peut avoir des pages qui ont la même structure et se partagent des composants, sans dupliquer la moindre ligne de code.