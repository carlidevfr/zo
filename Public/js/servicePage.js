import Display from './class/Display.js';
import Data from './class/Data.js';


const DISPLAY = new Display();
const DATA = new Data();


let servicesContainer = document.getElementById('services-display-services');

window.addEventListener("load", () => {
    // Evènements au chargement de la page

    let servicesUrl = '/apigetservices';
    DATA.getFetchData(servicesUrl, DISPLAY.sanitizeHtml, DISPLAY.displayServices, servicesContainer)

})