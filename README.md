# Subida de Archivos con PHP

Este proyecto consiste en un formulario sencillo que permite seleccionar un archivo desde el equipo y enviarlo al servidor para guardarlo dentro de una carpeta específica.

---

##  Objetivo

Aprender a trabajar con formularios HTML y el procesamiento de archivos en PHP mediante el uso de la superglobal `$_FILES`.

---

##  Estructura del Proyecto

```text
subir_archivos/
├── index.html
├── guardar.php
└── archivos/

```

* **`index.html`**: Formulario HTML que permite al usuario seleccionar y enviar un archivo.
* **`guardar.php`**: Script en PHP que recibe el archivo enviado y procesa su almacenamiento.
* **`archivos/`**: Carpeta destinada a almacenar los archivos subidos por los usuarios.

---

## Requisito Importante

Antes de probar el proyecto, **debes crear la carpeta `archivos**` dentro de la raíz del proyecto. Si la carpeta no existe, PHP no podrá guardar el archivo correctamente y se generará un error.

---

## ¿Cómo funciona?

1. El usuario selecciona un archivo desde su navegador a través de `index.html`.
2. El formulario envía los datos del archivo hacia `guardar.php`.
3. PHP procesa la solicitud recibida utilizando la variable superglobal `$_FILES`.
4. El archivo se almacena correctamente en la carpeta `archivos/`.

---

## Recomendaciones para la Ejecución

* **Servidor local:** Ejecuta y prueba el proyecto utilizando un entorno de servidor local como **XAMPP**, **WAMP** o **MAMP**.
* **Permisos de escritura:** Asegúrate de que la carpeta `archivos/` tenga otorgados los permisos de escritura necesarios para permitir guardar los elementos subidos.
