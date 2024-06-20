// AJAX: Asynchronous JavaScript And XML
// => Charger du HTML depuis un serveur en Javascript
// => Faire exécuter des requêtes HTTP au Javascript


// Rappel sur le code Asynchrone (non bloquant)
console.log("1 - Début du code");

let button = document.querySelector('a');

button.addEventListener('click', function(e) {
    e.preventDefault();
    console.log('2 - click')
})

console.log("3 - Suite du code")

// ---------------------------------------------------------------------------------------------------------------------
// Like / Unlike des Posts
// ---------------------------------------------------------------------------------------------------------------------


let likeButtons = document.querySelectorAll('.likes');

for (let button of likeButtons) {
    button.addEventListener('click', async function(e) {
        e.preventDefault(); // ici, on empêche le changement de page, d'URL

        /*
        // Façon "Old School", avec les Promises (promesses)
        // On déclenche la requête vers le serveur
        console.log("1 - Début de la requete vers le serveur")
        fetch('http://localhost:8000/posts/42/like')
            .then(function() {
                // Le code quand la promesse est tenue "resolue"
                console.log("3 - La requete a été traitée par le serveur avec succes")
            })
            .catch(function() {
                // Le code quand la promesse est rompue "rejetée"
                console.log("3 - La requete a échouée");
            })

        // ... on attend qu'il réponde
        console.log("2 - On attend que le serveur ait fini de traiter la requete");
        */



        console.log('Lancement de la requête sans rechargement !');

        // On affiche un élément qui montre que quelque chose se passe
        // button.querySelector('.spinner-border').classList.remove('d-none');

        // On déclenche la requete et on attend la réponse
        const response = await fetch(button.href);

        // On affiche qq chose en fonction de la réponse
        if (response.ok) {
            button.querySelector('i').className = "bi-heart-fill";
        } else {
            button.classList.add('test-error');
            button.textContent = "La requête a échoué, réessayez plus tard."
        }

        // On masque le spinner
        // button.querySelector('.spinner-border').classList.add('d-none');

    });
}
