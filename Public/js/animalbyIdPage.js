import Display from './class/Display.js';
import Data from './class/Data.js';


const DISPLAY = new Display();
const DATA = new Data();


let animauxContainer = document.getElementById('animal-display-animaux');
let veteContainer = document.getElementById('animal-display-veterinaire');

let idAnimal = document.getElementById('data').getAttribute('data');

window.addEventListener("load", () => {
    // Evènements au chargement de la page

    let animalUrl = '/apigetanimauxbyidanimal?animal=' + idAnimal;
    DATA.getFetchData(animalUrl, DISPLAY.sanitizeHtml, DISPLAY.displayImg, animauxContainer, 'carroussel-animaux-api')

    let vetelUrl = '/apigetrapportbyidanimal?animal=' + idAnimal;
    DATA.getFetchData(vetelUrl, DISPLAY.sanitizeHtml, DISPLAY.displayRapportsVete, veteContainer)

})