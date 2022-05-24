/**
 * Afficher le formulaire d'édition pour la fiche étudiante
 */

let editCours = document.getElementById('editCours');
let cancelCours = document.getElementById('cancelCours');
let showFormCours = document.getElementById('editTitleCours');
let editFormCours = document.getElementById('editFormCours');
let showDetailsCours = document.getElementById('titleCours');
let noteStudent = document.getElementsByClassName('noteStudent');
let deleteCours = document.getElementById('deleteCours');


// On affiche le formulaire d'édition
if(editCours){
    editCours.addEventListener('click', function(){
        showFormCours.classList = '';

        showDetailsCours.classList = "hidden ";
        editCours.classList = "hidden";

        if(editFormCours){
            editFormCours.classList = 'titleSection';
        }

        if(deleteCours){
            deleteCours.classList = "hidden";
        }


        for(let i = 0; i< noteStudent.length; i++){
            noteStudent[i].classList = "noteStudent";
        }

    });
}

// On cache le formulaire d'édition
if(cancelCours){
    cancelCours.addEventListener('click', function(){
        showFormCours.classList = 'hidden titleSection';
        showDetailsCours.classList = 'titleSection';
        editCours.classList = " editCours zoom";

        if(editFormCours){
            editFormCours.classList = 'hidden titleSection';
        }

        if(deleteCours){
            deleteCours.classList = "cancelInformation zoom transition";
        }


        for(let i = 0; i< noteStudent.length; i++){
            noteStudent[i].classList = "hidden noteStudent";
        }
    });
}



/**
 * Afficher le formulaire d'édition pour les cours
 */

let edit = document.getElementById('editStudent');
let cancel = document.getElementById('cancelStudent');
let showForm = document.getElementById('formEditStudent');
let showDetails = document.getElementById('detailsStudent');


// On affiche le formulaire d'édition
if(edit){
    edit.addEventListener('click', function(){
        showForm.classList = '';
        showDetails.classList = "hidden";
    });
}

if(cancel){
    // On cache le formulaire d'édition
    cancel.addEventListener('click', function(){
        showForm.classList = 'hidden';
        showDetails.classList = 'left-section';
        deleteCours.classList = "cancelInformation zoom";
    });
}

/**
 * Ajout + delete des intervenants
 */



// Récupération du bouton qui permte de rajouter des intervenants
const newIntervenant = document.getElementById('addNewIntervenant')

if(newIntervenant){
    // Div qui permet de récupérer la liste de tous les intervenants
    const listNewIntervenant = document.querySelector('.listNewIntervenant')

    const elementIntervenant = document.querySelectorAll('.campusNameSelected');

    for(let i=0; i < elementIntervenant.length; i++){

        deleteExistingIntervenants(document.getElementById('add_cours_form_intervenants_' + i));
    }

    function deleteExistingIntervenants(item){

        const removeFormButton = document.createElement('p');
        removeFormButton.innerHTML = '<i class="fa-solid fa-xmark zoom transition deleteIntervenant"></i>';

        item.append(removeFormButton);

        removeFormButton.addEventListener('click', (e) => {
            e.preventDefault();
            // remove the li for the tag form
            item.remove();
        });
    }

// Toutes les actions qui vont se passer quand l'utilisateur va cliquer sur newIntervenant
    const addFormToCollection = (e) => {

        //On récupere le dataset de l'id : addNewIntervenant pour permettre de trouver la class associé (la liste)
        let  listNewIntervenant = document.querySelector('.' + e.currentTarget.dataset.collectionholderclass);

        // On créer un élément random pour pouvoir rajouter les informations
        const item = document.createElement('p');

        item.classList = 'newIntervenantContent';

        // On remplace le __name__ par l'index de l'intervenant créé
        item.innerHTML = listNewIntervenant
            .dataset
            .prototype
            .replace(
                /__name__/g,
                listNewIntervenant.dataset.index
            );

        listNewIntervenant.appendChild(item);

        deleteExistingIntervenants(item);

        listNewIntervenant.dataset.index++;
    };

    newIntervenant.addEventListener("click", addFormToCollection);

}


