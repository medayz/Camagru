
/************
 **  CAMERA
 ************/

const   video = document.querySelector("#video video");
const   take_pic = document.querySelector("#take-pic");
const   save_pic = document.querySelector("#save-pic");
const   canvas = document.querySelector("canvas");
const   pics_div = document.querySelector("#pics-bar");
const   upload = document.querySelector('input[type="file"]');
const   stickers = document.querySelectorAll('.sticker');
const   stickers_upload = document.querySelectorAll('.sticker-upload');

if (document.getElementById("cancel")) {
    document.getElementById("cancel").addEventListener('click', function () {
        let show = document.getElementById("show-upload");
        document.querySelectorAll('.superposable').forEach(sticker => {
            sticker.remove();
        });
        document.querySelector('#save-pic').disabled = true;
        show.style.display = "none";
        document.querySelector("body").style.overflow = "";
    });
}
window.addEventListener('keyup', function (ev) {
    if (ev.key == "Escape") {
        let show = document.getElementById("show-upload");
        document.querySelectorAll('.superposable').forEach(sticker => {
            sticker.remove();
        });
        document.querySelector('#save-pic').disabled = true;
        show.style.display = "none";
        document.querySelector("body").style.overflow = "";
    }
});

stickers.forEach(sticker => {
    make_sticker_chosable(sticker, document.querySelector("video"));
});

stickers_upload.forEach(sticker => {
    make_sticker_chosable(sticker, document.querySelector("#uploaded"));
});

if (video) {
    navigator.mediaDevices.getUserMedia({video: true, audio: false})
        .then(function (stream) {
            video.srcObject = stream;
            video.play();
        })
        .catch(function (err) {
            // console.log("An error occurred: " + err)
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
                pics.paths.forEach(function(path) {
                    pics_html += '<div class="pic"><img class="img" src="' + path + '"><div class="delete"></div></div>';
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
        stickers.forEach(sticker => {
            sticker.remove();
        });
        document.querySelector('#take-pic').disabled = true;
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
                        let pics = JSON.parse(this.responseText);
                        let pics_html = "";
                        pics.forEach(function (path) {
                            pics_html += '<div class="pic"><img class="img" src="' + path + '"><div class="delete"></div></div>';
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
            pics.paths.forEach(function(path) {
                pics_html += '<div class="pic"><img class="img" src="' + path + '"><div class="delete"></div></div>';
            });
            pics_div.innerHTML = pics_html;
            delete_pics_event();
            document.querySelector('#show-upload').style.display = "none";
            document.querySelector("body").style.overflow = "";
            if (pics.err !== '')
                alert(pics.err);
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
    stickers.forEach(sticker => {
        sticker.remove();
    });
    document.querySelector('#save-pic').disabled = true;
    xhr.send("img=" + JSON.stringify(img));
}

if (upload) {
    upload.addEventListener('change', function() {
        if (upload.files && upload.files[0]) {
            let reader = new FileReader();

            reader.onload = e => {
                document.querySelectorAll('.superposable').forEach(sticker => {
                    sticker.remove();
                });
                document.querySelector('#take-pic').disabled = true;
                document.querySelector('#uploaded').setAttribute("src", e.target.result);
                document.querySelector('#show-upload').style.display = "block";
            };

            reader.readAsDataURL(upload.files[0]);
        }
    });
}

function make_sticker_chosable(sticker, neighbor) {

    sticker.addEventListener('click', function () {
        if (neighbor.tagName === 'VIDEO')
            document.querySelector('#take-pic').disabled = false;
        else
            document.querySelector('#save-pic').disabled = false;
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
		let video_elem = document.querySelector('video');
		if (video_elem) {
        	if (faces.includes(this.id)) {
            	superposable.style.width = Math.round(video_elem.offsetWidth * 46.88 / 100) + "px";
        	}
        	else {
            	superposable.style.width = Math.round(document.querySelector('video').offsetWidth * 15.63 / 100) + "px";
        	}

		}	else {

			superposable.remove();
		}
        superposable.addEventListener('dblclick', function (e) {
            this.remove();
            if (!document.querySelector('.superposable')) {
                if (neighbor.tagName === 'VIDEO')
                    document.querySelector('#take-pic').disabled = true;
                else
                    document.querySelector('#save-pic').disabled = true;
            }
        });
        superposable.style.top =  neighbor.offsetTop + "px";
        superposable.style.left = neighbor.offsetLeft + "px";
        neighbor.insertAdjacentElement('afterend', superposable);
        dragElement(superposable, neighbor);
    });
}

function dragElement(elmnt, container) {
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
