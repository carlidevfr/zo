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

function ValidatePassword(password) {
    // Regex to check the password criteria:
    // - Au moins 12 caractères
    // - Au moins 1 maj
    // - Au moins 1 minuscule
    // - Au moins 1 chiffre
    // - Au moins 1 caractère spécial
    const regex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{12,}$/;
    return regex.test(password);
}

let emailInput = document.querySelector('#email')       // on sélectionne l'input email
let passwordInput = document.querySelector('#password'); // On sélectionne l'input password
let emailLabel = document.querySelector('#email-label') // on sélectionne le label email
let form = document.querySelector('#form')
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
    let error = ''
    for (let count = 0; count < form.elements.length; count++) {
        if (form.elements[count].name === 'email') { // on vérifie si le mail est ok
            if (ValidateEmail(form.elements[count].value)) {

            } else if (form.elements[count].value.trim() === '') {
                // Si vide ne se passe rien
            } else {
                error += 1
            }
        }
        if (form.elements[count].name === 'password') { // On vérifie si le mot de passe est ok
            if (ValidatePassword(form.elements[count].value)) {
                // Mot de passe validé

            } else if (form.elements[count].value.trim() === '') {
                // Si vide ne se passe rien
            } else {
                error += 1;
            }
        }
    }
    if (error !== '') {
        alert("Le formulaire ne s'est pas envoyé, email ou mdp invalide")
        event.preventDefault()
        errorMessage.innerHTML = "Le formulaire ne s'est pas envoyé, email ou mdp invalide"
    } else {
        errorMessage.innerHTML = ""
    }
})