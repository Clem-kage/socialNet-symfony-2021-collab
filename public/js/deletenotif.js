let deleteButtons = document.querySelectorAll('.delete');
let showMenu = document.querySelector('.menu-notif');
let nbrNotifs = document.querySelector('.countnotif');
let displaydrop = document.querySelector('.display-drop');


for (let btn of deleteButtons){
    btn.addEventListener('click', async function (e) {
        e.preventDefault();

        showMenu.classList.add("show")
        btn.parentElement.className = 'd-none'

        let id = btn.dataset.notif
        nbrNotifs.textContent--

        if (nbrNotifs.textContent >0){
            $('.dropdown-menu').click(function (e) {
                e.stopPropagation();
            })}
        else {
            showMenu.style.display="none"
            displaydrop.disabled = true;
            displaydrop.style.opacity = 1;
        }

        let response = await fetch("/deletenotif/" + id)
    })
}


