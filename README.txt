CONFIGURACIÓN
=============
- Lanzar los servicios: En la carpeta principal lanzar `docker compose -d`
- Vue: Ir al apartado de Vue.

Ojo: Cambiar la IP en application/config/database.php

PUERTOS Y SERVICIOS
===================
- Codeigniter => 4001 http | 4002 https
- MYSQL => 13306
- PHPMYADMIN => 8888

VUE
===
- La parte de vue está en application/vue
- Es necesario hacer yarn install dentro de application/vue para instalar las bibliotecas.
- yarn build despliega el index.php en application/views/vue/index.php y los assets en assets/dist
- Se puede ejecutar "yarn run dev" para lanzar vue en modo desarrollo.

PRODUCCIÓN
==========
- Configurar database.php, config.php
- Configurar rest.php para añadir cors si es necesario.
- Configurar vue/config/prod.env.js y establecer la nueva red.
