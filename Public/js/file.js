function validateFileUpload(event) {
    // Récupère l'élément input file
    let fileInput = document.getElementById('addElementImg');

    // Récupère les fichiers à envoyer
    let files = fileInput.files;

    // Liste des extensions autorisées (ajoutez ici les extensions que vous souhaitez autoriser)
    let allowedExtensions = ['jpg', 'jpeg', 'png'];
    let maxSizeInBytes = 0.1 * 1024 * 1024;
    // 100ko

    // Boucle à travers chaque fichier sélectionné
    for (let i = 0; i < files.length; i++) {
        let fileName = files[i].name;
        let fileExtension = fileName.split('.').pop().toLowerCase();
        let fileSize = files[i].size;
        // Taille du fichier en octets

        // Vérifie si l'extension du fichier est autorisée
        if (allowedExtensions.indexOf(fileExtension) === -1) {
            event.preventDefault();
            // Empêche l'envoi du formulaire
            // Affiche un message d'erreur
            alert('Le fichier ' + fileName + ' n\'est pas autorisé. Veuillez sélectionner un fichier avec une extension ' + allowedExtensions.join(', '));
            return false; // Empêche l'envoi du formulaire
        }

        // Vérifie si la taille du fichier est supérieure à la limite autorisée
        if (fileSize > maxSizeInBytes) {
            event.preventDefault();
            // Empêche l'envoi du formulaire

            // Affiche un message d'erreur
            alert('Le fichier ' + fileName + ' dépasse la taille maximale autorisée de 100 Ko.');
            return false; // Empêche l'envoi du formulaire
        }
    }

    return true; // Autorise l'envoi du formulaire si tous les fichiers sont valides
}

// Attache la fonction de validation au formulaire avant l'envoi
let form = document.getElementById('addelement');
form.onsubmit = function () {
    return validateFileUpload(event);
};
