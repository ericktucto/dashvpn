# Backend principal

Con este backend tienes un CRUD de peers. Tambien puedes configurar el servidor,
pero es una tarea a futuro.

## Instalación

1. Clonar el repositorio: `git clone https://github.com/ericktucto/dashvpn.git`
2. Instalar dependencias: `composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist`
3. Crea la base de datos con ayuda de las migraciones, estan en la carpeta `databases/migrations`
debes ejecutarlas una por una con `sqlite3 {nombre_basedatos}.db < {migracion}.sql`
4. Crear llaves publica y privada: `bash keygen.sh`, quedate con la llave privada.
La publica la debes colocar en la raíz del proyecto `sharedlinks`
5. Debes copiar la configuración `config.example.yml` a `config.yml`, te digo para
que sirve cada claves:

    - env: Debe tener el valor `production` si lo desplegaras en producción, aunque
    si lo tienes privado el acceso al backend, daría igual el valor que pongas.
    Pero solo puede tener valores `local`, `testing` y `production`

    - databases: No cambies, esta pensado para sqlite3 el proyecto, es su conexión
    a la base de datos, tal vez puedas cambiar `database.path` y colocar un nombre
    diferente.

    - shared_api: Esta es la url es donde se encuentra el backend publico, para
    compartir los peers. Esto es debido aque el backend principal hace petición
    http para crear estos links.

    - private_key: La llave privada que generaste con el paso 4. Usa path completo.

    - cors: Configuracion de cors, no cambies.

    - jwt.key: Debes colocar un string, por ejemplo puedes usar este comando `openssl rand -hex 64`

    - jwt.expires: Tiempo en segundos, por ejemplo 3600.

    - data.config_dir: Carpeta donde se crean los archivos de configuración de los
    peers, debes tener permisos de escritura y lectura en ella (no lo termines en `/`)

    - data.ip: Ip pública del servidor de tu vpn, wireguard si debe estar expuesto
    asi que debes colocar la ip publica de ese vps que contiene el wireguard.

    - data.dns: Puedes colocar las de google o de cloudflare. Tambien puedes colocar
    la ip de tu Pi-Hole

6. Crea instala un servidor web, puede ser nginx, apache2, caddy, traeffik, lighttpd, etc.
No expongas el puerto, que viva en localhost.

