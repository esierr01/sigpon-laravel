import './bootstrap'; // Esto carga la configuración de axios, etc.
import * as bootstrap from 'bootstrap'; // Importa la librería JS de Bootstrap

// Esta línea es la magia: la hace disponible globalmente para tus vistas
window.bootstrap = bootstrap; 