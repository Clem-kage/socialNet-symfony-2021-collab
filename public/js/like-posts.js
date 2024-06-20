/*
1. Quand on clique sur un bouton Like, afficher l'id du Post cliqué dans la console
2.
 */

console.log("File loaded !");

document.addEventListener('DOMContentLoaded', function() {

    let buttons = document.querySelectorAll('.btn-like');

    for (let btn of buttons) {

        btn.addEventListener('click',
            async function(event)
                {await doLikeButton(event,btn);});

    }
});

async function doLikeButton(event,btn)
{
    event.preventDefault();

    if (btn.dataset.isLoading == "true") {
        console.log('Déja en train de charger !');
        return;
    }

    // On récupère la valeur de l'attribut "data-post" qu'on
    // à écrit en PHP sur la balise <a>
    let id = btn.dataset.post;

    // Faire une requete au serveur /posts/{id}/like
    // (mise a jour de la BDD)
    btn.dataset.isLoading = true;

    // Mettre à jour la vue (le html / css)

    // Solution 2 : c'est le client qui rend le nouveau HTML (via JS)
    // si je like deja
    if (btn.querySelector('i').classList.contains("bi-heart-fill")) {
        btn.querySelector('.counter').textContent--;
        btn.querySelector('i').className = "bi-heart";
    } else {
        btn.querySelector('.counter').textContent++;
        btn.querySelector('i').className = "bi-heart-fill";
    }

    let response = await fetch("/posts/" + id + "/like");
    btn.dataset.isLoading = false;

    if (!response.ok) {
        if (btn.querySelector('i').classList.contains("bi-heart-fill")) {
            btn.querySelector('.counter').textContent--;
            btn.querySelector('i').className = "bi-heart";
        } else {
            btn.querySelector('.counter').textContent++;
            btn.querySelector('i').className = "bi-heart-fill";
        }
    }

}








