

let canceldeleteOffer = document.getElementById('cancelValidation');
let deleteOffer = document.getElementById('deleteOffer');
let modalDeleteOffer = document.getElementById('modalDeleteOffer');

if(deleteOffer){
    deleteOffer.addEventListener('click', function (){

        modalDeleteOffer.classList = "containerValidationMessage";

    });

    canceldeleteOffer.addEventListener('click', function(){

        modalDeleteOffer.classList = "hidden";
    });
}