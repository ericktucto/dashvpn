# DASHVPN

Dashboard ligero para Wireguard. Es para un uso básico. Adminstra tus peers.

## ¿Que contiene?

- Dashboard (vue3 + tailwindcss + shadcn + pinia + vue-router + axios)
- Backend (Touch Framework + sqlite3)
- Web Público (Touch Framework + sqlite3)

El frontend es vue3 + tailwindcss + shadcn + pinia + vue-router + axios, no debe
ser expuesto al internet. Debes tener acceso de forma privada (por ejemplo mediante
tunel ssh). El Backend es Touch Framework + sqlite3, que debe no debe ser expuesto,
al igual que el frontend debes tener acceso de forma privada (por ejemplo mediante
tunel ssh). El Web Público es Touch Framework + sqlite3, que debe ser expuesto al
internet, por que sirve para compartir los peers mediante links y un código de 6
dígitos.

## TODOS

Existen varias tareas y puedes encontrarlas en el archivo `TODO.md`.

## Instalación

1. Clonar el repositorio: `git clone https://github.com/ericktucto/dashvpn.git`
2. Levantar el servidor web: En la raíz del proyecto, ejecutar `composer install`,
luego configura tu servidor web, por ejemplo con nginx (recuerda apuntar a
`public` en lugar de `src`). No expongas el puerto, que viva en localhost, para
mas detalles mira `BUILD.md`
3. Levantar frontend: Entra a la carpeta `frontend` y ejecuta `npm ci` y luego
ejecuta `npm run build`, luego configura tu servidor web, por ejemplo con nginx
(recuerda apuntar a `dist` en lugar de `src`). No expongas el puerto, que viva en
localhost.
4. Web público: Entra a la carpeta `sharedlinks` y ejecuta `composer install` y
luego configura tu servidor web, por ejemplo con nginx (recuerda apuntar a
`public` en lugar de `src`). Este debe ser público, para que puedan compartir los
peers que crees. Para mas detalles mira `sharedlinks/BUILD.md`
5. Debes agregar pasar el script `wg-manager.sh` como si binario con el siguiente
comando `sudo cp wg-manager.sh /usr/local/bin/wg-manager`, y debe permitir al usuario
`www-data` ejecutar el binario con sudo sin la necesidad de ingresar contraseñas.
Usa `visudo` y agrega `www-data ALL=(ALL) NOPASSWD: /usr/local/bin/wg-manager`

## ¿Como contribuir al proyecto?

1. Crea fork y mandas PR.
2. Puedes ayudarme a mejorar el frontend, backend o web público. Creando pruebas
unitarias o terminando la lista de `TODO.md`
3. Creando issues. Si encuentras un bug, lo mejor es crear un issue.

## FAQ

- ¿Por que Touch Framework?
Este proyecto salió por la necesidad de tener un dashboard minimalista, tengo un
vps con estas caracteristicas:
| Recurso        | Valor                  |
| -------------- | ---------------------- |
| CPU            | 2 vCPU (AMD EPYC 7551) |
| RAM            | ~1 GB                  |
| Disco          | 45 GB SSD              |
| Swap           | 4.7 GB                 |
| Virtualización | KVM                    |
tambien mis necesidades con un vpn wireguard es básico, nada complejo.

- ¿Por que no otro dashboard (como wg-easy)?
Si tu vps o servidor te permite, usa wg-easy, esta este proyecto resuelve necesidades
mías por el vps que tengo.

- ¿Se dar ayuda monetaria?
Si, aunque actualmente no tengo patreon, ko-fi, etc. puedes escribirme a [erick@ericktucto.com](mailto:erick@ericktucto.com)
mas a delante estare agregando esas formas de ayuda monetaria.

- ¿Por que php?
El proyecto cumple.

