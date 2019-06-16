console.log("Hello World!");
const inps = document.querySelectorAll('.inp');
const errs = document.querySelectorAll('.err');

errs.forEach(err => {
    if (err.innerHTML !== "") {
        err.hidden = false;
        err.style.padding = "8px 5%";
        err.style.borderRadius = "0px 0px 4px 4px";
    } else {
        err.style.padding = "0px";
        err.hidden = true;
    }
});

inps.forEach(inp => {
    console.log(inp.nextElementSibling.innerHTML);
    if (inp.nextElementSibling.innerHTML !== "") {
        inp.style.borderRadius = "4px 4px 0px 0px";
        if (inp.value !== "")
            inp.style.color = "#f00";
    }
});


/*************
**  NAV_BAR
************/

document.addEventListener('scroll', function() {
    document.querySelector("#navbar").style.backgroundColor = "rgba(45, 48, 71, 1)";
});


/************
**  CAMERA
************/

const video = document.querySelector("#video video");
if (video) {
    navigator.mediaDevices.getUserMedia({video: true, audio: false})
        .then(function (stream) {
            video.srcObject = stream;
            video.play();
        })
        .catch(function (err) {
            console.log("An error occurred: " + err)
        });
}

const   take_pic = document.querySelector("#take-pic");
if (take_pic)
    take_pic.addEventListener('click', function(ev){
        takepicture();
        ev.preventDefault();
    }, false);

const   canvas = document.querySelector("canvas");
const   pics_div = document.querySelector("#pics-bar");
// var     new_pic = document.querySelector("#new-pic");
const width = 640;
const height = 480;
function takepicture() {
    // new_pic.parentElement.hidden = false;
    if (width && height) {
        var context = canvas.getContext('2d');
        canvas.width = width;
        canvas.height = height;
        context.filter = "hue-rotate(90deg)";
        context.drawImage(video, 0, 0, width, height);
        var data = canvas.toDataURL('image/png');
        // new_pic.setAttribute('src', data);
        let xhr = new XMLHttpRequest();
        xhr.open("POST", 'home/submitPic', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                let pics = JSON.parse(this.responseText);
                let pics_html = "";
                pics.forEach(function(path) {
                    console.log(path);
                    pics_html += '<div class="pic"><img class="img" src="http://localhost/camagru/img/Users_pics/' + decodeURIComponent(path) + '"></div>';
                });
                pics_div.innerHTML = pics_html;
            }
        }
        xhr.send("img=" + encodeURIComponent(data));
    }   else {
        clearphoto();
    }
}

function clearphoto() {
    var context = canvas.getContext('2d');
    context.fillStyle = "#AAA";
    context.fillRect(0, 0, canvas.width, canvas.height);

    var data = canvas.toDataURL('image/png');
    photo.setAttribute('src', data);
}

/****************
**  MENU
****************/
var menu_btn = document.querySelector("#menu-btn");
var menu = document.querySelector(".menu");

menu_btn.addEventListener('click', function () {
    if (menu.style.display === "none") {
        // menu.style.height = "0px";
        menu.style.display = "block";
    }   else {
        // menu.style.height = "144px";
        menu.style.display = "none";
    }
});

window.addEventListener('resize', function () {
    if (window.innerWidth > 800) {
        menu.style.display = "inline";
    }   else {
        menu.style.display = "none";
    }
});

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

var like_btns = document.querySelectorAll(".like");
like_btns.forEach(function (btn) {
    btn.addEventListener('click', function (ev) {
        ev.stopImmediatePropagation();
        let pic_name = this.parentElement.parentElement.previousElementSibling.src.split('/').pop();

        let xhr = new XMLHttpRequest();
        if (this.className === "liked") {
            xhr.open("POST", 'gallery/unlikePic', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    console.log(this.responseText);
                    btn.className = "like";
                    loadLikes();
                }
            };
            xhr.send("pic=" + pic_name);
            return  null;
        }
        xhr.open("POST", 'gallery/likePic', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                loadLikes();
            }
        };
        xhr.send("pic=" + pic_name);
    });
});

loadLikes();


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
                com_elem.innerHTML = "<span class=\"user\">" + com.username + "</span><br><span class=\"body\">" + com.content + "</span>";
                com_div.appendChild(com_elem);
            });
        }
    };
    xhr.send("pic=" + pic);
}

var comment = document.getElementById("new-comment") ? document.getElementById("new-comment").firstElementChild : null;

 function addComment(ev, new_com) {
    if (ev.keyCode === 13 && !ev.shiftKey) {
        console.log("hey fucker!");
        let text = encodeURIComponent(new_com);
        let url = window.getComputedStyle(document.getElementById("post-img")).backgroundImage.match("(?<=\\(\")(.*?)(?=\"\\))").shift();
        let xhr = new XMLHttpRequest();
        xhr.open("POST", 'gallery/submitComment', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                // let new_com = JSON.parse(this.responseText);
                loadComments(url.split('/').pop());
            }
        };
        let comm = {
            text: text,
            pic: url.split('/').pop()
        };
        console.log(comm);
        xhr.send("comment=" + JSON.stringify(comm));
    }
}

if (comment) {
    comment.addEventListener('keyup', function (ev) {
        addComment(ev, this.value);
    });
}
function wheel_event(ev) {
    ev.preventDefault();
}
function disable_scroll() {
    if (window.addEventListener) {
        window.addEventListener('DOMMouseScroll', wheel_event, false);
    }
}
function enable_scroll() {
    if (window.removeEventListener) {
        window.removeEventListener('DOMMouseScroll', wheel_event, false);
    }
}
 
 var all_pics = document.querySelectorAll(".gallery");
 all_pics.forEach(function (pic) {
     pic.addEventListener('click', function () {
         let show_post = document.getElementById("show-post");
         show_post.style.display = "block";
         show_post.lastElementChild.firstElementChild.style.backgroundImage = "url(\"" + this.firstElementChild.src + "\")";
         loadComments(this.firstElementChild.src.split('/').pop());
     });
 });
 if (document.getElementById("cancel")) {
     document.getElementById("cancel").addEventListener('click', function () {
         let show_post = document.getElementById("show-post");
         show_post.style.display = "none";
     });
 }
 window.addEventListener('keyup', function (ev) {
     if (ev.key == "Escape") {
         let show_post = document.getElementById("show-post");
         show_post.style.display = "none";
     }
});
 var delete_btns = document.querySelectorAll(".delete");
 delete_btns.forEach(btn => {
     btn.addEventListener('click', function () {
         let xhr = new XMLHttpRequest();
         xhr.open("POST", 'home/deletePic', true);
         xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
         xhr.onreadystatechange = function() {
             if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                 // console.log(this.responseText);
                 let pics = JSON.parse(this.responseText);
                 let pics_html = "";
                 pics.forEach(function(path) {
                     pics_html += '<div class="pic"><img class="img" src="http://localhost/camagru/img/Users_pics/' + decodeURIComponent(path) + '"><div class="delete"></div></div>';
                 });
                 pics_div.innerHTML = pics_html;
             }
         };
         xhr.send("pic=" + this.parentElement.firstElementChild.src.split('/').pop());
     });
 });


