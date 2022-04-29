
// Récupération du bouton qui permte de rajouter des intervenants
const newSubject = document.getElementById('addNewSubject')

if(newSubject){
    // Div qui permet de récupérer la liste de tous les intervenants
    const listNewSubject = document.querySelector('.listNewSubject')

    const elementSubject = document.querySelectorAll('.campusNameSelected');

    for(let i=0; i < elementSubject.length; i++){
        deleteExistingSubjects(document.getElementById('edit_intervenant_form_subjects_' + i));
    }

    function deleteExistingSubjects(item){

        const removeFormButton = document.createElement('p');
        removeFormButton.innerHTML = '<i class="fa-solid fa-xmark zoom transition deleteIntervenant"></i>';

        item.append(removeFormButton);

        removeFormButton.addEventListener('click', (e) => {
            e.preventDefault();
            // remove the li for the tag form
            item.remove();
        });
    }

// Toutes les actions qui vont se passer quand l'utilisateur va cliquer sur newSubject
    const addFormToCollection = (e) => {

        //On récupere le dataset de l'id : addNewIntervenant pour permettre de trouver la class associé (la liste)
        let  listNewSubject = document.querySelector('.' + e.currentTarget.dataset.collectionholderclass);

        // On créer un élément random pour pouvoir rajouter les informations
        const item = document.createElement('p');

        item.classList = 'newIntervenantContent';

        // On remplace le __name__ par l'index de l'intervenant créé
        item.innerHTML = listNewSubject
            .dataset
            .prototype
            .replace(
                /__name__/g,
                listNewSubject.dataset.index
            );

        listNewSubject.appendChild(item);

        deleteExistingSubjects(item);

        listNewSubject.dataset.index++;
    };

    newSubject.addEventListener("click", addFormToCollection);

}