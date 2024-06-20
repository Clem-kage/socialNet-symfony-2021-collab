window.addEventListener('DOMContentLoaded',mainLoad);

const nbPostsLoaded = 5;

function mainLoad() {
    let postList = document.querySelector(".postList");
    let spinner = postList.querySelector(".spinner");
    let nbPosts = postList.querySelectorAll('.post').length;

    let options = {
        root: null,
        rootMargin: '0px',
        threshold: 1.0
    }

    let observer = new IntersectionObserver(load,options);

    observer.observe(spinner);

    async function load(entries, observer)
    {
        for (let entry of entries){
            if(entry.intersectionRatio>=1){
                entry.target.classList.toggle('hidden');
                observer.unobserve(entry.target);

                let authorId = spinner.getAttribute("data-load");

                let response =
                    await fetch("/post/load?from="+(nbPosts+1)
                        +"&number="+nbPostsLoaded
                        +"&authorId="+authorId);

                let htmlContent = await response.text();

                let posts = htmlContent.split('///');

                let newItem;


                for(let post of posts)
                {
                    if(post!==""){
                        newItem = document.createElement('li');
                        newItem.classList.add('mb-3');
                        newItem.innerHTML = post;
                        postList.insertBefore(newItem,spinner.parentElement);
                        nbPosts++;

                        let likeButton = newItem.querySelector(".btn-like");


                        likeButton.addEventListener('click',
                            async function(event)
                            {doLikeButton(event,likeButton);});
                    }
                }

                if(htmlContent.length===0){
                    entry.target.classList.toggle('d-none');
                } else {
                    entry.target.classList.toggle('hidden');
                    observer.observe(entry.target);
                }

                console.log("posts loaded");
            }
        }
    }
}

