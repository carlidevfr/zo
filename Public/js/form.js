function ValidateEmail(mail)
// paramètre : input du formulaire
// sortie : alert en cas d'erreur et true ou false
{
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
        return (true)
    } else {
        return (false)
    }
}

let emailInput = document.querySelector('#email')       // on sélectionne l'input email
let emailLabel = document.querySelector('#email-label') // on sélectionne le label email
let form = document.querySelector('form')
let errorMessage = document.querySelector('#submit-state')


emailInput.addEventListener('blur', () => { // on vérifie au lâché de cellule
    if (ValidateEmail(emailInput.value)) {
        emailInput.style.color = 'black'
    } else {
        emailInput.style.color = 'red'
        alert("Votre email est invalide!")
    }
})

form.addEventListener('submit', (event) => { // on vérifie les données à l'envoi du formulaire
    error = ''
    for (let count = 0; count < form.elements.length; count++) {
        if (form.elements[count].name === 'email') { // on vérifie si le mail est ok
            if (ValidateEmail(form.elements[count].value)) {
            } else {
                error += 1
            }
        }
    }
    if (error !== '') {
        alert("Le formulaire ne s'est pas envoyé")
        event.preventDefault()
        errorMessage.innerHTML = "Le formulaire ne s'est pas envoyé"
    }else{
        errorMessage.innerHTML = ""
    }
})