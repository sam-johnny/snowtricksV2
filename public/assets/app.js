/* Bouton loadMore commentaire */
function loadMoreComments() {
    var postId = document.getElementById("comments").dataset.post;
    var offsetPaginator = parseInt(document.getElementById("comments").dataset.offset) + 1;
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function () {
        document.getElementById("comments").innerHTML += this.responseText;
        document.getElementById("comments").dataset.offset = offsetPaginator;
    }
    xhttp.open("GET", "/loadmoreComments/" + postId + "?page=" + offsetPaginator);
    xhttp.send();
}

/* Bouton media */
let button = document.getElementById("media-button");
let divMedia = document.getElementById("media");
button.addEventListener("click", () => {
    if (getComputedStyle(divMedia).display !== "none") {
        divMedia.style.display = "none";
    } else {
        divMedia.style.display = "block";
        button.style.display = "none";
    }
})

/* Bouton loadMore Post */
function loadMorePosts() {
    var offsetPaginator = parseInt(document.getElementById("posts").dataset.offset) + 1;
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function () {
        document.getElementById("posts").innerHTML += this.responseText;
        document.getElementById("posts").dataset.offset = offsetPaginator;
    }
    xhttp.open("GET", "/loadmorePosts?page=" + offsetPaginator);
    xhttp.send();
}






