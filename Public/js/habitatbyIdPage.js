import Display from './class/Display.js';
import Data from './class/Data.js';


const DISPLAY = new Display();
const DATA = new Data();


let animauxContainer = document.getElementById('habitats-display-animaux');
let idHabitat = document.getElementById('data').getAttribute('data');
console.log(idHabitat)

window.addEventListener("load", () => {
    // Evènements au chargement de la page

    let habitatUrl = '/apigetanimauxbyhabitat?habitat=' + idHabitat;
    DATA.getFetchData(habitatUrl, DISPLAY.sanitizeHtml, DISPLAY.displayAnimauxInHabitat, animauxContainer, 'carroussel-animaux-api', DISPLAY.displayImg)

})