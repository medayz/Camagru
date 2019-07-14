const   comment = document.getElementById("new-comment").firstElementChild;

/****************
 **  LIKE
 ****************/

function loadLikes() {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let likes = JSON.parse(this.responseText);
            let pics_divs = document.querySelectorAll('.gallery');
            pics_divs.forEach(function (pic) {
                let name = pic.firstElementChild.src.split('/').pop();
                pic.lastElementChild.firstElementChild.firstElementChild.firstElementChild.innerText = "0";
                likes.all_likes.forEach(function (like) {
                    if (like.pic_path === name) {
                        if (like.username === likes.loggedOn_user)
                            pic.lastElementChild.firstElementChild.lastElementChild.className = "liked";
                        pic.lastElementChild.firstElementChild.firstElementChild.firstElementChild.innerText =
                            (parseInt(pic.lastElementChild.firstElementChild.firstElementChild.firstElementChild.innerText) + 1).toString();
                    }
                });
            });
        }
    };
    xhr.open("GET", "gallery/getLikes", true);
    xhr.send();
}

function likes() {
    let like_btns = document.querySelectorAll(".like");
    like_btns.forEach(function (btn) {
        btn.addEventListener('click', function (ev) {
            ev.stopImmediatePropagation();
            let pic_name = this.parentElement.parentElement.previousElementSibling.src.split('/').pop();

            let xhr = new XMLHttpRequest();
            if (this.className === "liked") {
                xhr.open("POST", 'gallery/unlikePic', true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                        btn.className = "like";
                        loadLikes();
                    }
                };
                xhr.send("pic=" + pic_name);
                return ;
            }
            xhr.open("POST", 'gallery/likePic', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    loadLikes();
                }
            };
            xhr.send("pic=" + pic_name);
        });
    });

    loadLikes();
}

likes();


/***************
 **  NEW_COMMENT
 ***************/

function loadComments(pic) {
    let new_com = document.createElement("div");
    new_com.id = "new-comment";
    new_com.className = "comment";
    new_com.innerHTML = "<input id=\"com-inp\" type=\"text\" placeholder=\"comment this pic...\">";
    new_com.firstElementChild.addEventListener('keyup', function (ev) {
        addComment(ev, this.value);
    });

    let com_div = document.getElementById("comment-div");
    com_div.innerHTML = "";
    com_div.appendChild(new_com);
    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'gallery/getComments', true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let comments = JSON.parse(this.responseText);
            comments.forEach(function (com) {
                let com_elem = document.createElement("div");
                com_elem.className = "comment";
                let del_btn = document.createElement("div");
                del_btn.className = "delete delcom";
                del_btn.addEventListener('click', function (event) {
                    if (confirm("do you really want to delete this so important and interesting comment ?")) {
                        let xhr = new XMLHttpRequest();
                        xhr.open("POST", 'gallery/deleteComment', true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function () {
                            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                                if (this.responseText === "OK") {
                                    loadComments(com.pic_path);
                                }
                            }
                        };
                        xhr.send("comment=" + JSON.stringify(com));
                    }
                });
                com_elem.innerHTML = "<span class=\"user\"></span><br><span class=\"body\"></span></div>";
                com_elem.firstElementChild.appendChild(document.createTextNode(com.username));
                com_elem.lastElementChild.appendChild(document.createTextNode(com.content));
                com_elem.prepend(del_btn);
                com_div.appendChild(com_elem);
            });
        }
    };
    xhr.send("pic=" + pic);
}


function addComment(ev, new_com) {
    if (ev.keyCode === 13 && !ev.shiftKey) {
        let text = encodeURIComponent(new_com);
        let url = document.getElementById("post-img").className;
        let comm = {
            text: text,
            pic: url
        };
        let xhr = new XMLHttpRequest();
        xhr.open("POST", 'gallery/submitComment', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                loadComments(url);
                let xhr = new XMLHttpRequest();
                xhr.open("POST", 'gallery/sendMail', true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("comment=" + JSON.stringify(comm));
            }
        };
        xhr.send("comment=" + JSON.stringify(comm));
    }
}

function newPics() {
    let all_pics = document.querySelectorAll(".gallery");
    all_pics.forEach(function (pic) {
        pic.addEventListener('click', function () {
            let show_post = document.getElementById("show-post");
            let n_likes = document.createElement('span');
            let took_pic = document.createTextNode(this.firstElementChild.src.split('_').pop().split('.').shift());
            show_post.style.display = "block";
            document.querySelector('#post-img').style.backgroundImage = "url(\"" + this.firstElementChild.src + "\")";
            document.querySelector('#post-img').className = this.firstElementChild.src.split('/').pop();
            n_likes.innerText = this.lastElementChild.firstElementChild.firstElementChild.firstElementChild.innerText + " likes";
            n_likes.addEventListener('click', function() {
                if (!this.nextElementSibling) {
                    let likes_div = document.createElement('div');
                    likes_div.style.padding = "10px";
                    likes_div.style.height = "100%";
                    likes_div.style.overflow = "auto";
                    likes_div.style.display = "flex";
                    likes_div.style.alignItems = "center";
                    likes_div.style.justifyContent = "center";
                    get_likes(pic.firstElementChild.src.split('/').pop(), likes_div, this.parentElement);
                    document.querySelector('#post').style.gridTemplateRows = "3fr 2fr 2fr";
                }  else {
                    this.nextElementSibling.remove();
                    document.querySelector('#post').style.gridTemplateRows = "6fr .7fr 5fr";
                }
            });
            document.querySelector('#user-likes').innerHTML = "";
            document.querySelector('#user-likes').appendChild(took_pic);
            document.querySelector('#user-likes').appendChild(n_likes);
            document.querySelector('#user-likes').style.overflow = "hidden";
            document.querySelector("body").style.overflow = "hidden";
            loadComments(this.firstElementChild.src.split('/').pop());
        });
    });
}
newPics();

if (document.getElementById("cancel")) {
    document.getElementById("cancel").addEventListener('click', function () {
        let show = document.getElementById("show-post");
        show == null ? (show = document.getElementById("show-upload")) : loadLikes();
        show.style.display = "none";
        document.querySelector('#user-likes').innerText = "";
        document.querySelector('#post').style.gridTemplateRows = "6fr .7fr 5fr";
        document.querySelector("body").style.overflow = "";
    });
}
window.addEventListener('keyup', function (ev) {
    if (ev.key == "Escape") {
        let show = document.getElementById("show-post");
        show == null ? (show = document.getElementById("show-upload")) : loadLikes();
        show.style.display = "none";
        document.querySelector("#user-likes").innerText = "";
        document.querySelector('#post').style.gridTemplateRows = "6fr .7fr 5fr";
        document.querySelector("body").style.overflow = "";
    }
});

function   get_likes(pic_url, likes_div, parent) {
    let likes = [];
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "gallery/getPicLikes");
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            likers = JSON.parse(this.responseText);
            let likes = "<ul style='list-style-type: square;'>";
            likers.forEach(obj => {
                likes += "<li style='padding: 5px;'>" + obj.username + "</li>";
            });
            likes += "</ul>";
            likes_div.innerHTML = likes;
            parent.appendChild(likes_div);
        }
    };
    xhr.send("pic=" + encodeURIComponent(pic_url));
    return likes;
}

if (document.querySelector('#post-img')) {
    document.querySelector('#post-img').addEventListener('mouseover', function () {
        this.firstElementChild.style.display = "block";
    });
    document.querySelector('#post-img').addEventListener('mouseout', function () {
        this.firstElementChild.style.display = "none";
    });
    document.querySelector('#post-img').addEventListener('click', function () {
        let xhr = new XMLHttpRequest();
        pic_name =this.className;
        xhr.open("POST", 'gallery/ic', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("pic=" + pic_name);
    });
}

function    loadPics() {
    let pics = document.querySelector('.pics');

    loadPics.index = (typeof loadPics.index === 'undefined') ? 20 : loadPics.index + 4;
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'gallery/newRowPics');
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            let new_pics = JSON.parse(this.responseText);
            new_pics.forEach(pic => {
                let div = document.createElement('div');
                let img = document.createElement('img');
                div.className = "pic gallery";
                img.className = "img";
                img.src = pic.path;
                div.appendChild(img);
                div.innerHTML += '<div class="over-img"><div class="engagement"><span><span>0</span> likes <span>0</span> comments</span><div class="comments"></div><div class="like"></div></div></div>';
                pics.appendChild(div);
            });
            likes();
            newPics();
        }
    };
    xhr.send('index=' + loadPics.index);
}
window.addEventListener('scroll', function scroll_handler() {
    let pics_div = document.querySelector('.pics');
    if (this.innerHeight + this.scrollY >= pics_div.getBoundingClientRect().bottom) {
        loadPics();
    }
});
