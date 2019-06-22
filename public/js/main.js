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
        canvas.width = width;
        canvas.height = height;
        var context = canvas.getContext('2d');
        // context.filter = "hue-rotate(90deg)";
        // context.filter = "grayscale(100%)";
        context.scale(-1, 1);
        context.drawImage(video, width * -1, 0, width, height);
        let data = canvas.toDataURL('image/png');
        let xhr = new XMLHttpRequest();
        xhr.open("POST", 'home/submitPic', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                console.log(this.responseText);
                let pics = JSON.parse(this.responseText);
                let pics_html = "";
                pics.forEach(function(path) {
                    console.log(path);
                    pics_html += '<div class="pic"><img class="img" src="http://localhost/camagru/img/Users_pics/' + decodeURIComponent(path) + '"><div class="delete"></div></div>';
                });
                pics_div.innerHTML = pics_html;
                delete_pics_event();
            }
        }
        let img = {
            pic : encodeURIComponent(data),
            sticker : document.querySelector('#superposable').className,
            x : document.querySelector('#superposable').offsetLeft - 20,
            y : document.querySelector('#superposable').offsetTop - 20,
            width : document.querySelector('#superposable').offsetWidth,
            height : document.querySelector('#superposable').offsetHeight
        };
        xhr.send("img=" + JSON.stringify(img));
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
                com_elem.innerHTML = "<span class=\"user\">" + com.username + "</span><br><span class=\"body\">" + com.content + "</span></div>";
                com_elem.prepend(del_btn);
                com_div.appendChild(com_elem);
            });
        }
    };
    xhr.send("pic=" + pic);
}

var comment = document.getElementById("new-comment") ? document.getElementById("new-comment").firstElementChild : null;

 function addComment(ev, new_com) {
    if (ev.keyCode === 13 && !ev.shiftKey) {
        let text = encodeURIComponent(new_com);
        let url = window.getComputedStyle(document.getElementById("post-img")).backgroundImage.match("(?<=\\(\")(.*?)(?=\"\\))").shift();
        let xhr = new XMLHttpRequest();
        xhr.open("POST", 'gallery/submitComment', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                loadComments(url.split('/').pop());
            }
        };
        let comm = {
            text: text,
            pic: url.split('/').pop()
        };
        xhr.send("comment=" + JSON.stringify(comm));
    }
}

 var all_pics = document.querySelectorAll(".gallery");
 all_pics.forEach(function (pic) {
     pic.addEventListener('click', function () {
         let show_post = document.getElementById("show-post");
         show_post.style.display = "block";
         show_post.lastElementChild.firstElementChild.style.backgroundImage = "url(\"" + this.firstElementChild.src + "\")";
         document.querySelector("body").style.overflow = "hidden";
         loadComments(this.firstElementChild.src.split('/').pop());
     });
 });
 if (document.getElementById("cancel")) {
     document.getElementById("cancel").addEventListener('click', function () {
         let show_post = document.getElementById("show-post");
         show_post.style.display = "none";
         document.querySelector("body").style.overflow = "";
         loadLikes();
     });
 }
 window.addEventListener('keyup', function (ev) {
     if (ev.key == "Escape") {
         let show_post = document.getElementById("show-post");
         show_post.style.display = "none";
         document.querySelector("body").style.overflow = "";
         loadLikes();
     }
});
 delete_pics_event();
 function   delete_pics_event() {
     let delete_btns = document.querySelectorAll(".delete");
     delete_btns.forEach(btn => {
         btn.addEventListener('click', function () {
             if (confirm("do you really want to delete this amazing masterpiece ?")) {
                 let xhr = new XMLHttpRequest();
                 xhr.open("POST", 'home/deletePic', true);
                 xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                 xhr.onreadystatechange = function () {
                     if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                         // console.log(this.responseText);
                         let pics = JSON.parse(this.responseText);
                         let pics_html = "";
                         pics.forEach(function (path) {
                             pics_html += '<div class="pic"><img class="img" src="http://localhost/camagru/img/Users_pics/' + decodeURIComponent(path) + '"><div class="delete"></div></div>';
                         });
                         pics_div.innerHTML = pics_html;
                         delete_pics_event();
                     }
                 };
                 xhr.send("pic=" + this.parentElement.firstElementChild.src.split('/').pop());
             }
         });
     });
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
         pic_name = getComputedStyle(this).backgroundImage.match("(?<=\\(\")(.*?)(?=\"\\))").shift().split('/').pop();
         xhr.open("POST", 'gallery/likePic', true);
         xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
         xhr.send("pic=" + pic_name);
     });
 }

var stickers = document.querySelectorAll('.sticker');
var superposable = document.querySelector('#superposable');
stickers.forEach(function (sticker) {
    sticker.addEventListener('click', function () {
        if (superposable.src === this.firstElementChild.src) {
            superposable.src = "";
            superposable.className = "";
            superposable.style.display = "none";
        }
        else {
            superposable.src = this.firstElementChild.src;
            superposable.className = this.id;
            superposable.style.display = "block";
            if (["rasbiri.png", "10vitesse.png", "55.png", "aymane.png", "ozaazaa.png"].includes(this.id)) {
                superposable.style.width = "300px";
            }
            else {
                superposable.style.width = "100px";
            }
        }
    });
});

dragElement(superposable);
function dragElement(elmnt) {
    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0, top = 0, left = 0;


    elmnt.onmousedown = dragMouseDown;

    function dragMouseDown(e) {
        e.preventDefault();
        // get the mouse cursor position at startup:
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        // call a function whenever the cursor moves:
        document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
        e.preventDefault();
        // calculate the new cursor position:
        let img_size = document.querySelector('#superposable').offsetHeight;
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;

        // set the element's new position:
        let video_bounds = document.querySelector("video").getBoundingClientRect();
        if ((elmnt.offsetTop - pos2) < video.offsetTop) {
            top = video.offsetTop;
        }   else if ((elmnt.offsetTop - pos2) > (video_bounds.bottom - video_bounds.top + 20 - img_size)) {
            top = video_bounds.bottom - video_bounds.top + 20 - img_size;
        }   else {
            top = elmnt.offsetTop - pos2;
        }

        if ((elmnt.offsetLeft - pos1) < video.offsetLeft) {
            left = video.offsetLeft;
        }   else if ((elmnt.offsetLeft - pos1) > (video_bounds.right - video_bounds.left + 20 - img_size)) {
            left = video_bounds.right - video_bounds.left + 20 - img_size;
        }   else {
            left = elmnt.offsetLeft - pos1;
        }

        elmnt.style.top = top + "px";
        elmnt.style.left = left + "px";
    }

    function closeDragElement() {
        // stop moving when mouse button is released:
        document.onmouseup = null;
        document.onmousemove = null;
    }
}
