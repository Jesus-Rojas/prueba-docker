# Prueba

## Requerimientos:

- Laravel 8.*
- Php 8.*

## Pre-Instalacion

- Se debe agregar la base de datos junto con el user y password en el .env, si no existe haga copia del .env.example

## Instalacion

```console
unknown@unknown$ php artisan key:generate
unknown@unknown$ php artisan jwt:secret
unknown@unknown$ php artisan migrate:fresh --seed
unknown@unknown$ php artisan serve
```

### Postman: 
- prueba.postman_collection.json (es el nombre del archivo se encuentra en la raiz del proyecto)

<br>
<br>

<img src="./MER.png" width="50%">