/**
 * Afficher le formulaire d'édition pour la fiche étudiante
 */

let editCours = document.getElementById('editCours');
let cancelCours = document.getElementById('cancelCours');
let showFormCours = document.getElementById('editTitleCours');
let editFormCours = document.getElementById('editFormCours');
let showDetailsCours = document.getElementById('titleCours');

// On affiche le formulaire d'édition
editCours.addEventListener('click', function(){
    showFormCours.classList = '';
    editFormCours.classList = 'titleSection';
    showDetailsCours.classList = "hidden ";
    editCours.classList = "hidden";
});


// On cache le formulaire d'édition
cancelCours.addEventListener('click', function(){
    showFormCours.classList = 'hidden titleSection';
    editFormCours.classList = 'hidden titleSection';
    showDetailsCours.classList = 'titleSection';
    editCours.classList = " editCours zoom";
});