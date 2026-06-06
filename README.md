# PLANTILLA DE LARAVEL 12 - PROYECTO CON AUTENTICACION LARAVEL UI/BOOTSTRAP

## Desarrollado por: Emmanuel Sierra (Junio 2026)

Tecnologías:

[![Laravel](https://img.shields.io/badge/Laravel-%23FF2D20.svg?logo=laravel&logoColor=white)](#)

<br/>

## Pasos para clonar y utilizar plantilla (en terminal):
<div align="left">
    <h3>Paso 1 (Clonar repositorio GitHub) - Ejecutar en terminal:</h3>
    <p style="background-color: #272c5c; color: white; padding: 10px; border-radius: 5px; max-width: 800px;">
        git clone https://github.com/esierr01/plantilla-laravel-12-proyecto-aut-ui-bootstrap.git</p>
    <h3>Paso 2 (instalar dependencias de PHP) - Ejecutar en terminal:</h3>
    <p style="background-color: #272c5c; color: white; padding: 10px; border-radius: 5px; max-width: 800px;">
        composer install</p>
    <h3>Paso 3 (crear archivo .env) - Ejecutar en terminal:</h3>
    <p style="background-color: #272c5c; color: white; padding: 10px; border-radius: 5px; max-width: 800px;">
        ren .env-ejemplo .env</p>
    <p>NOTA: editar archivo .env con tus credenciales de base de datos, por defecto tiene una db Sqlite3</p>
    <h3>Paso 4 (Instalar dependencias de NodeJS) - Ejecutar en terminal:</h3>
    <p style="background-color: #272c5c; color: white; padding: 10px; border-radius: 5px; max-width: 800px;">
        npm install</p>
    <h3>Paso 5 (Ejecutar Migraciones para que se creen las tablas necesarias) - Ejecutar en terminal:</h3>
    <p style="background-color: #272c5c; color: white; padding: 10px; border-radius: 5px; max-width: 800px;">
        php artisan migrate</p>
</div>

<br/>

## Pasos para ejecutar proyecto (en dos terminales):
<div align="left">
    <h3>Paso 1 (Compilar assets (CSS y JS)) - Ejecutar en terminal 1:</h3>
    <p style="background-color: #272c5c; color: white; padding: 10px; border-radius: 5px; max-width: 800px;">
        npm run dev</p>
    <h3>Paso 2 (Levantar servidor web) - Ejecutar en terminal 2:</h3>
    <p style="background-color: #272c5c; color: white; padding: 10px; border-radius: 5px; max-width: 800px;">
        php artisan serve</p>
</div>



