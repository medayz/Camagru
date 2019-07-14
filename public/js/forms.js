const   inps = document.querySelectorAll('.inp');
const   errs = document.querySelectorAll('.err');

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
    if (inp.nextElementSibling.innerHTML !== "") {
        inp.style.borderRadius = "4px 4px 0px 0px";
        if (inp.value !== "")
            inp.style.color = "#f00";
    }
});
