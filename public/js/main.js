console.log("Hello World!");
const   inps = document.querySelectorAll('.inp');
const   errs = document.querySelectorAll('.err');
const   video = document.querySelector("#video video");
const   take_pic = document.querySelector("#take-pic");
const   save_pic = document.querySelector("#save-pic");
const   canvas = document.querySelector("canvas");
const   pics_div = document.querySelector("#pics-bar");
const   upload = document.querySelector('input[type="file"]');
const   menu_btn = document.querySelector("#menu-btn");
const   menu = document.querySelector(".menu");
const   comment = document.getElementById("new-comment") ? document.getElementById("new-comment").firstElementChild : null;
const   stickers = document.querySelectorAll('.sticker');
const   stickers_upload = document.querySelectorAll('.sticker-upload');

errs.forEach(err => {
    if (err.innerHTML !== "") {
        err.hidden = false;
        err.style.padding = "8px 5%";
        err.style.borderRadius = "0 0 4px 4px";
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

if (take_pic)
    take_pic.addEventListener('click', function(ev){
        takepicture();
        ev.preventDefault();
    }, false);

if (save_pic)
    save_pic.addEventListener('click', function(ev){
        save_picture();
        ev.preventDefault();
    }, false);

const width = 640;
const height = 480;
function takepicture() {
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
                let pics = JSON.parse(this.responseText);
                let pics_html = "";
                pics.forEach(function(path) {
                    pics_html += '<div class="pic"><img class="img" src="http://localhost/camagru/img/Users_pics/' + decodeURIComponent(path) + '"><div class="delete"></div></div>';
                });
                pics_div.innerHTML = pics_html;
                delete_pics_event();
            }
        };
        let img = {
            pic : encodeURIComponent(data),
            stickers : []
        };
        let stickers = document.querySelectorAll('.superposable');
        stickers.forEach(sticker => {
            img.stickers.push(
                {
                    name: sticker.id,
                    x: sticker.offsetLeft - 20,
                    y: sticker.offsetTop - 20,
                    width: sticker.offsetWidth,
                    height: sticker.offsetHeight
                }
            );
        });
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

function save_picture() {
    let pic = document.querySelector('#uploaded');
    let data = pic.src;
    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'home/submitPic', true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            let pics = JSON.parse(this.responseText);
            let pics_html = "";
            pics.forEach(function(path) {
                pics_html += '<div class="pic"><img class="img" src="http://localhost/camagru/img/Users_pics/' + decodeURIComponent(path) + '"><div class="delete"></div></div>';
            });
            pics_div.innerHTML = pics_html;
            delete_pics_event();
            document.querySelector('#show-upload').style.display = "none";
            document.querySelector("body").style.overflow = "";
        }
    };
    let img = {
        pic : encodeURIComponent(data),
        stickers : []
    };
    let stickers = document.querySelector('#edit-uploaded > div').querySelectorAll('.superposable');
    stickers.forEach(sticker => {
        img.stickers.push(
            {
                name: sticker.id,
                x: sticker.offsetLeft - pic.offsetLeft,
                y: sticker.offsetTop - pic.offsetTop,
                width: sticker.offsetWidth,
                height: sticker.offsetHeight
            }
        );
    });
    xhr.send("img=" + JSON.stringify(img));
}

if (upload) {
    upload.addEventListener('change', () => {
        if (upload.files && upload.files[0]) {
            let reader = new FileReader();

            reader.onload = e => {
                 document.querySelector('#uploaded').setAttribute("src", e.target.result);
                 document.querySelector('#show-upload').style.display = "block";
            };

            reader.readAsDataURL(upload.files[0]);
        }
    });
}

/****************
**  MENU
****************/

menu_btn.addEventListener('click', function () {
    if (menu.style.display === "none") {
        menu.style.display = "block";
    }   else {
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
                        console.log(this.responseText);
                        btn.className = "like";
                        loadLikes();
                    }
                };
                xhr.send("pic=" + pic_name);
                return null;
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

function newPics() {
     let all_pics = document.querySelectorAll(".gallery");
     all_pics.forEach(function (pic) {
         pic.addEventListener('click', function () {
             let show_post = document.getElementById("show-post");
             let took_pic = document.createTextNode('hamid');
             let n_likes = document.createElement('span');
             let url = window.getComputedStyle(document.getElementById("post-img")).backgroundImage.match("(?<=\\(\")(.*?)(?=\"\\))").shift();
             show_post.style.display = "block";
             document.querySelector('#post-img').style.backgroundImage = "url(\"" + this.firstElementChild.src + "\")";
             console.log(this.lastElementChild.firstElementChild.firstElementChild.firstElementChild.innerText);
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
                     let likes = "<ul style='list-style-type: square;'>";
                     let likers = get_likes(pic.firstElementChild.src.split('/').pop());
                     likers.forEach(obj => {
                         likes += "<li style='padding: 5px;'>" + obj.username + "</li>";
                     });
                     likes += "</ul>";
                     likes_div.innerHTML = likes;
                     this.parentElement.appendChild(likes_div);
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

 function   get_likes(pic_url) {
     let likes = [];
     let xhr = new XMLHttpRequest();
     xhr.open("POST", "gallery/getPicLikes", false);
     xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
     xhr.onreadystatechange = function() {
         if (this.readyState == 4 && this.status == 200) {
             likes = JSON.parse(this.responseText);
             return likes;
         }
     };
     xhr.send("pic=" + encodeURIComponent(pic_url));
     return likes;
 }

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

stickers.forEach(sticker => {
    make_sticker_chosable(sticker, document.querySelector("video"));
});

stickers_upload.forEach(sticker => {
    make_sticker_chosable(sticker, document.querySelector("#uploaded"));
});

window.addEventListener('load', function () {
    console.log("window loading...");
    console.log(this.innerHeight);
});
function    loadPics() {
    let pics = document.querySelector('.pics');

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
                img.src = 'http://' + window.location.hostname + "/camagru/img/Users_pics/" + pic.path;
                div.appendChild(img);
                div.innerHTML += '<div class="over-img"><div class="engagement"><span><span>0</span> likes <span>0</span> comments</span><div class="comments"></div><div class="like"></div></div></div>';
                pics.appendChild(div);
            });
            likes();
            newPics();
        }
    };
    xhr.send("index=" + document.querySelectorAll('.gallery').length);
}
window.addEventListener('scroll', function scroll_handler() {
    if( typeof scroll_handler.scroll === 'undefined' ) {
        scroll_handler.scroll = 200;
    }
    if (this.scrollY >= scroll_handler.scroll) {
        scroll_handler.scroll = this.scrollY + 200;
        console.log("loading pics... " + scroll_handler.scroll);
        loadPics();
    }   else {
        console.log(scroll_handler.scroll);
    }
});

function make_sticker_chosable(sticker, neighbor) {
    sticker.addEventListener('click', function () {
        let superposable = document.createElement("img");
        superposable.className = "superposable";
        superposable.src = this.firstElementChild.src;
        superposable.id = this.id;
        superposable.style.display = "block";
        let faces = [
            "rasbiri.png",
            "10vitesse.png",
            "55.png",
            "aymane.png",
            "ozaazaa.png",
            "ayman2.png",
            "abida.png",
            "allali.png",
            "abida2.png",
            "afaddoul.png"
        ];
        if (faces.includes(this.id)) {
            superposable.style.width = Math.round(document.querySelector('video').offsetWidth * 46.88 / 100) + "px";
        }
        else {
            superposable.style.width = Math.round(document.querySelector('video').offsetWidth * 15.63 / 100) + "px";
        }
        superposable.addEventListener('dblclick', function (e) { this.remove(); });
        console.log(neighbor.getBoundingClientRect().top);
        console.log(neighbor.getBoundingClientRect().left);
        superposable.style.top =  neighbor.offsetTop + "px";
        superposable.style.left = neighbor.offsetLeft + "px";
        console.log(superposable.style);
        neighbor.insertAdjacentElement('afterend', superposable);
        dragElement(superposable, neighbor);
    });
}

function dragElement(elmnt, container) {
    console.log(elmnt);
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
        if (e.target.className !== "superposable")
            return null;
        let img_size = e.target.className === "superposable" ? e.target.offsetHeight : null;
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;

        // set the element's new position:
        let video_bounds = container.getBoundingClientRect();
        if ((elmnt.offsetTop - pos2) < container.offsetTop) {
            top = container.offsetTop;
        }   else if ((elmnt.offsetTop - pos2) > (video_bounds.bottom - video_bounds.top + container.offsetTop - img_size)) {
            top = video_bounds.bottom - video_bounds.top + container.offsetTop - img_size;
        }   else {
            top = elmnt.offsetTop - pos2;
        }

        if ((elmnt.offsetLeft - pos1) < container.offsetLeft) {
            left = container.offsetLeft;
        }   else if ((elmnt.offsetLeft - pos1) > (video_bounds.right - video_bounds.left + container.offsetLeft - img_size)) {
            left = video_bounds.right - video_bounds.left + container.offsetLeft - img_size;
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
