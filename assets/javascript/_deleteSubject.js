

let cancelDeleteCours = document.getElementById('cancelValidation');
let deleteCours = document.getElementById('deleteCours');
let modalDeleteCours = document.getElementById('modalDeleteCours');

if(deleteCours){
    deleteCours.addEventListener('click', function (){

        modalDeleteCours.classList = "containerValidationMessage";

    });

    cancelDeleteCours.addEventListener('click', function(){

        modalDeleteCours.classList = "hidden";
    });
}