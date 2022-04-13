/**
 * Afficher le formulaire d'édition pour la fiche étudiante
 */

let edit = document.getElementById('editStudent');
let cancel = document.getElementById('cancelStudent');
let showForm = document.getElementById('formEditStudent');
let showDetails = document.getElementById('detailsStudent');

// On affiche le formulaire d'édition
edit.addEventListener('click', function(){
    showForm.classList = '';
    showDetails.classList = "hidden";
});


// On cache le formulaire d'édition
cancel.addEventListener('click', function(){
    showForm.classList = 'hidden';
    showDetails.classList = 'left-section';
});