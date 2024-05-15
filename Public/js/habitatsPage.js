import Display from './class/Display.js';
import Data from './class/Data.js';


const DISPLAY = new Display();
const DATA = new Data();


let habitatsContainer = document.getElementById('habitats-display-habitats');

window.addEventListener("load", () => {
    // Evènements au chargement de la page

    let habitatsUrl = './apigetallhabitats';
    DATA.getFetchData(habitatsUrl, DISPLAY.sanitizeHtml, DISPLAY.displayHabitatsPage, habitatsContainer, 'carroussel-habitats-api', DISPLAY.displayImg)

})