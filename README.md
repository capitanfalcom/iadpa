# Pagina Iadpa

## Requesitos
- **Node.js**

## Instalar dependencias
	npm install

## Tareas Gulp
### Deploy
Dentro de la tarea deploy configurar la siguiente variable:
- **path**: carpeta donde se dejara el distribuible.

Por ejemplo.

	gulp deploy

## Configuracion de parametros de ambiente
### Frontend archivo app.js
- **REST_PATH**: url donde debe encontrar los servicios rest.
- **IMAGE_PATH**: url donde debe encontrar la carpeta que contiene las imagenes de los productos.
- **FLIPBOOK_PATH**: url donde debe encontrar la carpeta que contiene los flipbook.
- **sitekey**: key para el captcha en el front.

### Backend
### Frontend archivo config.php
- **PRODUCT_IMG**: carpeta donde se encuentran las imagenes.
- **DIR_FLIPBOOK**: carpeta donde se encuentra las imagenes para el flipbbok.
- **CAPTCHA_SECRET**: secret para el captcha en el back.
- **MAIL_HOST**: host del correo de donde se enviara los mensajes a los cientes.
- **MAIL_USUARIO**: usuario del correo de donde se enviara el mensaje a los clientes.
- **MAIL_CONTRASENA**: contraseña del correo de donde se enviara el mensaje a los clientes.
- **MAIL_CONTACTO**: mail del contacto que recibira mensaje de los clientes.
- **URL_HOME**: url del home de la pagina.
- **$db_host**: host de la base de datos.
- **$db_username**: usuario de la base de datos.
- **$db_password**: contraseña de la base de datos.
- **$db_name**: nombre de la base de datos.


