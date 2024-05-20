import Display from './class/Display.js';
import Data from './class/Data.js';


const DISPLAY = new Display();
const DATA = new Data();


window.addEventListener("load", () => {
    // Evènements au chargement de la page
    let habitatsMenuContainer = document.getElementById('habitat-menu');
    let habitatsUrl = '/apigetallhabitats';
    DATA.getFetchData(habitatsUrl, DISPLAY.sanitizeHtml, DISPLAY.displayMenuHabitats, habitatsMenuContainer)
})