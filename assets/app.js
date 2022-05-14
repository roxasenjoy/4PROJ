/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base shared (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)

// All
import './styles/app.scss';

// Login
import './styles/login.scss';

// Layout
import './styles/shared/layout/footer.scss';
import './styles/shared/layout/header.scss';
import './styles/shared/_filterCampus.scss';

// Liste des pages
import './styles/dashboard.scss';
import './styles/notes.scss';
import './styles/intervenant.scss';
import './styles/settings.scss';
import './styles/cours.scss';
import './styles/comptability.scss';
import './styles/student.scss';
import './styles/offer.scss';

// Toutes les librairies
import './styles/libraries/hint.css'



// Variables
import './styles/variables.scss';
import './styles/error.scss';

// start the Stimulus application
import './bootstrap';
import './javascript/global.js';
import  './javascript/_deleteSubject.js';
import  './javascript/_addSubject.js';
import  './javascript/_addDateSubject.js';

