export default class Data {

    getFetchData(apiUrl, ResAction, resDom, id) {
        // Param : url à interroger + fonction de traitement du résultat + element du dom pour print le res

        fetch(apiUrl)    // Utilisation de fetch pour interroger l'API
            .then((response) => {
                if (response.ok) {
                    return response.json()
                } else {
                    console.error('Erreur de récupération des données:' + response.status)
                }
            })
            .then((data) => {
                ResAction(data, resDom, id)
            })
            .catch((error) => { console.error('Erreur de récupération des données:' + error) });
    }

}