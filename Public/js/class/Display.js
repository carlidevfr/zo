export default class Display {

  sanitizeHtml(text) {
    // Créez un élément HTML temporaire de type "div"
    const tempHtml = document.createElement('span');

    // Affectez le texte à l'élément temporaire pour que les entités HTML soient converties
    tempHtml.innerHTML = text;

    // Récupérez le contenu de l'élément temporaire
    // Cela va "neutraliser" ou "échapper" tout code HTML potentiellement malveillant
    return tempHtml.textContent;
  }

  displayImg(sanitizeHtml, data, resDom, id) {
    // Param : json des images +  element du dom pour print le res

    try {
      // Sélection de l'élément conteneur
      let container = resDom;

      // Vérifier si l'élément conteneur existe
      if (!container) {
        throw new Error('L\'élément conteneur n\'existe pas.');
      }

      // Vider le container
      container.innerHTML = '';

      // Créer le carroussel et Mettre les attributs
      let carousel = document.createElement('div');
      carousel.classList.add('carousel', 'slide');
      carousel.setAttribute('id', id);
      carousel.setAttribute('data-bs-ride', 'carousel');
      carousel.style.maxWidth = '600px';

      // Créer les indicateurs de carrousel
      let indicators = document.createElement('div');
      indicators.classList.add('carousel-indicators');

      // Créer les éléments du carrousel
      let carouselInner = document.createElement('div');
      carouselInner.classList.add('carousel-inner', 'col-11', 'col-xl-7', 'col-xxl-6');



      // Ajouter chaque élément au carrousel
      data.forEach((animal, index) => {
        let carouselItem = document.createElement('div');
        carouselItem.classList.add('carousel-item');
        if (index === 0) {
          carouselItem.classList.add('active');
        }

        let img = document.createElement('img');
        img.classList.add('d-block', 'w-100');

        if (animal.images !== undefined && animal.images.length > 0) {
          // S'il y a une entrée "image" dans le json
          img.setAttribute('src', 'data:' + sanitizeHtml(animal.images[0].type) + ';base64,' + sanitizeHtml(animal.images[0].data)); // Utiliser la première image
          img.setAttribute('alt', sanitizeHtml(animal.valeur));
        } else {
          // Si on traite directement le flux image
          img.setAttribute('src', 'data:' + sanitizeHtml(animal.type) + ';base64,' + sanitizeHtml(animal.data)); // Utiliser la première image
          img.setAttribute('alt', sanitizeHtml(animal.valeur));
        }

        carouselItem.appendChild(img);
        carouselInner.appendChild(carouselItem);

        // Créer un indicateur pour chaque élément
        let indicator = document.createElement('button');
        indicator.setAttribute('type', 'button');
        indicator.setAttribute('data-bs-target', '#' + id);
        indicator.setAttribute('data-bs-slide-to', index.toString());
        if (index === 0) {
          indicator.classList.add('active');
        }
        indicators.appendChild(indicator);
      });

      // Ajouter les boutons de contrôle au carrousel
      let prevButton = document.createElement('button');
      prevButton.classList.add('carousel-control-prev');
      prevButton.setAttribute('type', 'button');
      prevButton.setAttribute('data-bs-target', '#' + id);
      prevButton.setAttribute('data-bs-slide', 'prev');
      prevButton.innerHTML = '<span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span>';

      let nextButton = document.createElement('button');
      nextButton.classList.add('carousel-control-next');
      nextButton.setAttribute('type', 'button');
      nextButton.setAttribute('data-bs-target', '#' + id);
      nextButton.setAttribute('data-bs-slide', 'next');
      nextButton.innerHTML = '<span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span>';

      // Ajouter les éléments au carrousel
      carousel.appendChild(indicators);
      carousel.appendChild(carouselInner);
      carousel.appendChild(prevButton);
      carousel.appendChild(nextButton);

      // Ajouter le carrousel au conteneur
      container.appendChild(carousel);


    } catch (error) {
      console.error('Une erreur s\'est produite lors de l\'affichage des elements:', error);
    }
  }

  displayAccordion(sanitizeHtml, data, resDom, id) {
    try {

      // Sélection de l'élément conteneur
      let container = resDom;

      // Vérifier si l'élément conteneur existe
      if (!container) {
        throw new Error("L'élément conteneur n'existe pas.");
      }

      // Vider le container
      container.innerHTML = '';

      // Créer l'accordéon
      let accordion = document.createElement('div');
      accordion.classList.add('accordion');
      accordion.setAttribute('id', id);

      // Parcourir les données et créer chaque élément de l'accordéon
      data.forEach((item, index) => {
        // Créer l'élément d'accordéon
        let accordionItem = document.createElement('div');
        accordionItem.classList.add('accordion-item');

        // Créer l'en-tête de l'élément d'accordéon
        let header = document.createElement('h2');
        header.classList.add('accordion-header');
        header.setAttribute('id', `${id}heading${index}`);

        let button = document.createElement('button');
        button.classList.add('accordion-button');
        button.setAttribute('type', 'button');
        button.setAttribute('data-bs-toggle', 'collapse');
        button.setAttribute('data-bs-target', `#${id}collapse${index}`);
        button.setAttribute('aria-expanded', index === 0 ? 'true' : 'false');
        button.setAttribute('aria-controls', `${id}collapse${index}`);
        button.textContent = sanitizeHtml(item.valeur);

        // Ajouter le bouton à l'en-tête
        header.appendChild(button);

        // Créer le corps de l'élément d'accordéon
        let collapse = document.createElement('div');
        collapse.classList.add('accordion-collapse', 'collapse');
        collapse.setAttribute('id', `${id}collapse${index}`);
        collapse.setAttribute('aria-labelledby', `${id}heading${index}`);
        collapse.setAttribute('data-bs-parent', '#' + id);

        if (index === 0) {
          collapse.classList.add('show');
        }

        let body = document.createElement('div');
        body.classList.add('accordion-body');
        body.textContent = sanitizeHtml(item.description);

        // Ajouter le corps à la section collapse
        collapse.appendChild(body);

        // Ajouter l'en-tête et la section collapse à l'élément d'accordéon
        accordionItem.appendChild(header);
        accordionItem.appendChild(collapse);

        // Ajouter l'élément d'accordéon à l'accordéon
        accordion.appendChild(accordionItem);
      });

      // Ajouter l'accordéon au conteneur
      container.appendChild(accordion);
    } catch (error) {
      console.error("Une erreur s'est produite lors de l'affichage de l'accordéon :", error);
    }
  }

  displayServices(sanitizeHtml, data, resDom) {
    try {
      // Sélection de l'élément conteneur
      let container = resDom;

      // Vérifier si l'élément conteneur existe
      if (!container) {
        throw new Error("L'élément conteneur n'existe pas.");
      }

      // Vider le container
      container.innerHTML = '';

      // Parcourir les données et créer chaque article
      data.forEach((item, index) => {
        // Création de l'article
        let article = document.createElement('article');
        article.classList.add('col-11', 'col-xl-10', 'col-xxl-9', 'mt-5', 'row', 'row', 'justify-content-between', 'align-items-center');

        // Création de la première colonne (avec le titre)
        let firstColumn = document.createElement('div');
        firstColumn.classList.add('col-sm-12', 'col-lg-3', 'col-xl-3', 'col-xxl-3', 'background__tertiary', 'p-3', 'm-md-3', 'd-flex', 'justify-content-center', 'align-items-center');

        let firstColumnHeader = document.createElement('h2');
        firstColumnHeader.classList.add('text-white', 'text-center');
        firstColumnHeader.textContent = sanitizeHtml(item.valeur);

        firstColumn.appendChild(firstColumnHeader);

        // Création de la deuxième colonne (avec le paragraphe)
        let secondColumn = document.createElement('div');
        secondColumn.classList.add('col-sm-12', 'col-lg-8', 'col-xl-8', 'col-xxl-8', 'background__secondary', 'p-3', 'm-md-3', 'mt-3');

        let secondColumnContent = document.createElement('p');
        secondColumnContent.textContent = sanitizeHtml(item.description);

        secondColumn.appendChild(secondColumnContent);

        // Ajout des colonnes à l'article
        article.appendChild(firstColumn);
        article.appendChild(secondColumn);

        // Ajouter l'article au conteneur
        container.appendChild(article);
      });

    } catch (error) {
      console.error("Une erreur s'est produite lors de l'affichage des articles :", error);
    }
  }

  displayHabitatsPage(sanitizeHtml, data, resDom, id, displayImg) {
    try {

      // Sélection de l'élément conteneur
      let container = resDom;

      // Vérifier si l'élément conteneur existe
      if (!container) {
        throw new Error("L'élément conteneur n'existe pas.");
      }

      // Vider le container
      container.innerHTML = '';

      // Parcourir les données et créer chaque article
      data.forEach((item, index) => {
    
        // Création de l'article
        let article = document.createElement('article');
        article.classList.add('col-11', 'col-xl-10', 'col-xxl-11', 'mt-5', 'row', 'row', 'justify-content-between', 'align-items-center');

        // Création de la première colonne (avec le titre)
        let firstColumn = document.createElement('a');
        firstColumn.classList.add('col-sm-12', 'col-lg-3', 'col-xl-4', 'col-xxl-4', 'background__tertiary', 'p-3', 'm-md-3', 'd-flex', 'justify-content-center', 'align-items-center');
        firstColumn.href = '/nos-habitats/habitat?habitat=' + sanitizeHtml(item.id);

        let firstColumnHeader = document.createElement('h2');
        firstColumnHeader.classList.add('text-white', 'text-center');
        firstColumnHeader.textContent = sanitizeHtml(item.valeur);

        firstColumn.appendChild(firstColumnHeader);

        // Création de la deuxième colonne (avec l'image)
        let secondColumn = document.createElement('div');
        secondColumn.classList.add('col-sm-12', 'col-lg-8', 'col-xl-7', 'col-xxl-7', 'p-3', 'm-md-3', 'mt-3', 'd-flex', 'justify-content-center' );

        if (item.images !== undefined && item.images.length > 0) {
          // S'il y a des images
          displayImg(sanitizeHtml, item.images, secondColumn, index);

        } else {
          // Si aucune image
          secondColumn.textContent = 'Aucune image disponible';
        }

        // Ajout des colonnes à l'article
        article.appendChild(firstColumn);
        article.appendChild(secondColumn);

        // Ajouter l'article au conteneur
        container.appendChild(article);
      });

    } catch (error) {
      console.error("Une erreur s'est produite lors de l'affichage des articles :", error);
    }
  }

  displayAnimauxInHabitat(sanitizeHtml, data, resDom, id, displayImg) {
    try {

      // Sélection de l'élément conteneur
      let container = resDom;

      // Vérifier si l'élément conteneur existe
      if (!container) {
        throw new Error("L'élément conteneur n'existe pas.");
      }

      // Vider le container
      container.innerHTML = '';

      // Parcourir les données et créer chaque article
      data.forEach((item, index) => {
    
        // Création de l'article
        let article = document.createElement('article');
        article.classList.add('col-11', 'col-xl-10', 'col-xxl-11', 'mt-5', 'row', 'row', 'justify-content-between', 'align-items-center');

        // Création de la première colonne (avec le titre)
        let firstColumn = document.createElement('a');
        firstColumn.classList.add('col-sm-12', 'col-lg-3', 'col-xl-4', 'col-xxl-4', 'background__tertiary', 'p-3', 'm-md-3', 'd-flex', 'justify-content-center', 'align-items-center');
        firstColumn.href = '/animal?animal=' + sanitizeHtml(item.id);

        let firstColumnHeader = document.createElement('h2');
        firstColumnHeader.classList.add('text-white', 'text-center');
        firstColumnHeader.textContent = sanitizeHtml(item.valeur);

        firstColumn.appendChild(firstColumnHeader);

        // Création de la deuxième colonne (avec l'image)
        let secondColumn = document.createElement('div');
        secondColumn.classList.add('col-sm-12', 'col-lg-8', 'col-xl-7', 'col-xxl-7', 'p-3', 'm-md-3', 'mt-3', 'd-flex', 'justify-content-center' );

        if (item.images !== undefined && item.images.length > 0) {
          // S'il y a des images
          displayImg(sanitizeHtml, item.images, secondColumn, index);

        } else {
          // Si aucune image
          secondColumn.textContent = 'Aucune image disponible';
        }

        // Ajout des colonnes à l'article
        article.appendChild(firstColumn);
        article.appendChild(secondColumn);

        // Ajouter l'article au conteneur
        container.appendChild(article);
      });

    } catch (error) {
      console.error("Une erreur s'est produite lors de l'affichage des articles :", error);
    }
  }

  sayHello() {
    console.log(this.sanitizeHtml('"<>test'))
  }

}