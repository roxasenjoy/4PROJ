

let cancelDeleteIntervenant = document.getElementById('cancelValidation');
let deleteIntervenant = document.getElementById('deleteIntervenant');
let modalDeleteIntervenant = document.getElementById('modalDeleteIntervenant');

if(deleteIntervenant){
    deleteIntervenant.addEventListener('click', function (){

        modalDeleteIntervenant.classList = "containerValidationMessage shadow";

    });

    cancelDeleteIntervenant.addEventListener('click', function(){

        modalDeleteIntervenant.classList = "hidden";
    });
}