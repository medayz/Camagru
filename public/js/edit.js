const notif = document.querySelector("#notif");

notif.addEventListener('click', function () {
    if (this.className === 'active') {

        this.className = 'inactive';
        this.nextElementSibling.innerText = 'inactive';
        this.nextElementSibling.nextElementSibling.value = 'inactive';
    }   else if (this.className === 'inactive') {

        this.className = 'active';
        this.nextElementSibling.innerText = 'active';
        this.nextElementSibling.nextElementSibling.value = 'active';
    }
});
