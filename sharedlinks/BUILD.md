# Web publico

Para compartir los peers.

## Instalación

1. Clonar el repositorio: `git clone https://github.com/ericktucto/dashvpn.git`
2. Instalar dependencias: `composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist`
3. Muevete a la carpeta `sharedlinks`
4. Crea la base de datos con ayuda de las migraciones, estan en la carpeta `databases/migrations`
debes ejecutarlas una por una con `sqlite3 {nombre_basedatos}.db < {migracion}.sql`
5. Obtene tu clave publica, puedes verlo en el archivo `BUILD.md` del backend
principal, ejecuta el script con: `bash keygen.sh`, La publica la debes colocar
en la raíz del proyecto `sharedlinks`
6. Debes copiar la configuración `config.example.yml` a `config.yml`, te digo para
que sirve cada claves:

    - env: Debe tener el valor `production` si lo desplegaras en producción.

    - databases: No cambies, esta pensado para sqlite3 el proyecto, es su conexión
    a la base de datos, tal vez puedas cambiar `database.path` y colocar un nombre
    diferente.

    - cors: Configuracion de cors, no cambies.

    - key_public: La llave publica que generaste con el paso 5. Usa path completo.

6. Crea instala un servidor web, puede ser nginx, apache2, caddy, traeffik, lighttpd, etc.
No expongas el puerto, que viva en localhost.

