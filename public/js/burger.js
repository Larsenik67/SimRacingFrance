const burgerBtn = document.querySelector("#burger")

function slideMenu(toggle = true){
    const menuMobile = document.querySelector("#menu-links")
    if(toggle){
        menuMobile.classList.toggle("slideDown")
        burgerBtn.querySelector("i").classList.toggle("fa-times")
    }
    else{
        menuMobile.classList.remove("slideDown")
        burgerBtn.querySelector("i").classList.remove("fa-times")
    }
}

burgerBtn.addEventListener("click", function(event){ 
    event.stopPropagation()//ignore les évènements qui se propagent sur toi (de la part de tes parents)
    event.preventDefault()
    slideMenu()
})