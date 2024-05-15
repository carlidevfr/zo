import Display from './class/Display.js';
import Data from './class/Data.js';


const DISPLAY = new Display();
const DATA = new Data();

let animauxContainer = document.getElementById('home-display-animaux');
let habitatsContainer = document.getElementById('home-display-habitats');
let servicesContainer = document.getElementById('home-display-services');

window.addEventListener("load", () => {
    // Evènements au chargement de la page

    let animauxUrl = './apigetimganimaux';
    DATA.getFetchData(animauxUrl, DISPLAY.sanitizeHtml, DISPLAY.displayImg, animauxContainer, 'carroussel-animaux-api')

    let habitatsUrl = './apigetimghabitats';
    DATA.getFetchData(habitatsUrl, DISPLAY.sanitizeHtml, DISPLAY.displayImg, habitatsContainer, 'carroussel-habitats-api')

    let servicesUrl = './apigetservices';
    DATA.getFetchData(servicesUrl, DISPLAY.sanitizeHtml, DISPLAY.displayAccordion, servicesContainer, 'accordeon-services-api')

})