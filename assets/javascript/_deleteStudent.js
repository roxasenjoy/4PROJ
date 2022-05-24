

let cancelDeleteStudent = document.getElementById('cancelValidation');
let deleteStudent = document.getElementById('deleteStudent');
let modalDeleteStudent = document.getElementById('modalDeleteStudent');

if(deleteStudent){
    deleteStudent.addEventListener('click', function (){

        modalDeleteStudent.classList = "containerValidationMessage shadow";

    });

    cancelDeleteStudent.addEventListener('click', function(){

        modalDeleteStudent.classList = "hidden";
    });
}