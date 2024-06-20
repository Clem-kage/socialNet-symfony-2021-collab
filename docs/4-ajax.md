# Communiquer avec le serveur grâce à Javascript

Dans les applications web, on utilise l'URL pour communiquer. Le **client** informe le **serveur** qu'il veut effectuer une opération sur une ressource, grâce à une adresse bien précise

Pour cela, on peut par exemple utiliser les liens html `<a href="...">` , ou bien les formulaires `<form action="...">`. Quand on clique sur un lien, le navigateur se rend à l'adresse indiquée, et l'utilisateur voit donc la page changer. Il y a donc (re)chargement de page.

Dans les applications modernes, il est courant de faire exécuter des actions au serveur, sans recharger la page. Cela permet par exemple de conserver la valeur du scroll, de conserver la valeur des variables Javascript, etc

Pour cela, on a besoin de faire exécuter des **requêtes HTTP** à Javascript. C'est le mécanisme d'AJAX (Asynchronous Javascript And XML)

## Rappel sur l'asynchrone

Le JS est un langage qui permet de programmer de manière différée dans la temps.
Par exemple, on peut planifier l'exécution du code en réaction à un évènement (ex: au clic sur un bouton). En attendant que cet évènement se produise, le Javascript "passe à la suite" du code, et dès que l'évènement se produit, il exécute la fonction qu'on avait planifié (on appelle cette fonction de rappel un _callback_) 

> Attention, cela ne veut pas dire que Javascript sait faire plusieurs choses en même temps pour autant !

