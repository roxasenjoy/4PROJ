/**
 * Afficher le formulaire d'édition pour la fiche étudiante
 */

let editCours = document.getElementById('editCours');
let cancelCours = document.getElementById('cancelCours');
let showFormCours = document.getElementById('editTitleCours');
let editFormCours = document.getElementById('editFormCours');
let showDetailsCours = document.getElementById('titleCours');

// On affiche le formulaire d'édition
if(editCours){
    editCours.addEventListener('click', function(){
        showFormCours.classList = '';
        editFormCours.classList = 'titleSection';
        showDetailsCours.classList = "hidden ";
        editCours.classList = "hidden";
    });
}

// On cache le formulaire d'édition
if(cancelCours){
    cancelCours.addEventListener('click', function(){
        showFormCours.classList = 'hidden titleSection';
        editFormCours.classList = 'hidden titleSection';
        showDetailsCours.classList = 'titleSection';
        editCours.classList = " editCours zoom";
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
    });
}
