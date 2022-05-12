let addDate = document.getElementById('addHour');
let modalAddHours = document.getElementById('modalAddHours');
let cancelDate = document.getElementById('cancelDate');

if(addDate){
    addDate.addEventListener('click', function (){

        modalAddHours.classList = "modalAddHours shadow";

    });

    cancelDate.addEventListener('click', function(){

        modalAddHours.classList = "hidden";
    });
}