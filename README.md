# 📐 Baldor

> Aplicación web para aprender álgebra al estilo del _Álgebra de Baldor_: temas, ejercicios y seguimiento del progreso del estudiante. Construida con **Laravel + React (Inertia)**. Proyecto personal **en desarrollo**.

![Estado](https://img.shields.io/badge/Estado-En%20desarrollo-yellow?style=flat-square)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![React](https://img.shields.io/badge/React-19-61DAFB?style=flat-square&logo=react&logoColor=black)
![TypeScript](https://img.shields.io/badge/TypeScript-5-3178C6?style=flat-square&logo=typescript&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-4-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)
![Tests](https://img.shields.io/badge/Tests-Pest-F28D1A?style=flat-square)

---

## 🚧 Estado del proyecto

Baldor está en construcción. Ya cuenta con autenticación, configuración de usuario, modelo de datos para temas, ejercicios e intentos, un inicio conectado a la base de datos y el ciclo principal de estudio: el estudiante entra a un tema, resuelve ejercicios de opción múltiple y su avance queda guardado.

El contenido de álgebra todavía está en desarrollo y los datos actuales son de demostración.

> Nota sobre el contenido: este repositorio no incluye material del libro. Los temas y ejercicios que se agreguen serán propios, inspirados en el estilo de enseñanza de Baldor.

**Hecho**

- [x] Portada con descripción de la app
- [x] Registro, inicio y cierre de sesión
- [x] Recuperación de contraseña (rutas y pantallas listas)
- [x] Configuración de cuenta: perfil, seguridad y apariencia (modo claro/oscuro)
- [x] Interfaz completamente en español
- [x] Modelo de datos: temas, ejercicios y progreso por usuario
- [x] Conectar el inicio a la base de datos
- [x] Inicio del estudiante con tres bloques: continuar donde lo dejó, progreso general y temas
- [x] Vista de un tema con su descripción, barra de progreso y lista de ejercicios
- [x] Vista de un ejercicio de opción múltiple
- [x] Resolver ejercicios: validación de la respuesta, registro del intento y mensaje de resultado
- [x] Pruebas automatizadas y análisis estático

**Pendiente**

- [ ] Pruebas automáticas de las vistas de temas y ejercicios, y del guardado de intentos
- [ ] Que el botón "Continuar" del inicio lleve al siguiente ejercicio
- [ ] Botón "Siguiente ejercicio" al terminar uno
- [ ] Contenido real de álgebra (temas y ejercicios propios)
- [ ] Envío real de correos para recuperar la contraseña
- [ ] Logo e imágenes definitivas
- [ ] Página "Acerca de"

---

## ¿Qué hace esta app?

Los usuarios pueden registrarse, iniciar sesión y entrar a su **inicio**, que muestra:

1. **Continuar donde lo dejaste:** acceso directo al último tema o ejercicio (o una invitación a empezar por el primero si es un usuario nuevo).
2. **Progreso general:** barra de porcentaje, ejercicios resueltos y temas completados.
3. **Temas:** tarjetas con el estado de cada tema (sin empezar, en curso o completado) y su avance.

Desde el inicio pueden abrir un tema y ver:

- Su descripción y su barra de progreso.
- La lista de ejercicios, cada uno marcado como **resuelto** o **pendiente**.

Al abrir un ejercicio eligen una de las opciones y pulsan **Revisar respuesta**: la app guarda el intento y muestra si fue correcto o no. Se pueden hacer varios intentos, y un ejercicio cuenta como resuelto cuando se acierta al menos una vez.

Desde la configuración pueden editar su perfil, cambiar la contraseña y elegir la apariencia de la interfaz.

Es un proyecto personal de práctica: el objetivo es construir una plataforma de estudio completa con Laravel y React, paso a paso.

---

## Stack técnico

| Capa                 | Tecnología                                                    |
| -------------------- | ------------------------------------------------------------- |
| Backend              | PHP 8.3 · Laravel 13                                          |
| Autenticación        | Laravel Fortify (registro, login, recuperación de contraseña) |
| Frontend             | React 19 · TypeScript · Inertia.js 3                          |
| Estilos              | Tailwind CSS 4 · componentes Radix UI · iconos Lucide         |
| Rutas tipadas        | Laravel Wayfinder                                             |
| Base de datos        | SQLite (por defecto)                                          |
| Herramientas         | Vite · Laravel Pint · PHPStan                                 |
| Pruebas              | Pest                                                          |
| Integración continua | GitHub Actions                                                |

---

## Decisiones técnicas destacadas

**Aplicación sin API separada.** Inertia conecta Laravel con React: las rutas y la autenticación viven en Laravel y las pantallas son componentes de React, sin construir una API REST aparte.

**Ejercicios de opción múltiple.** Cada ejercicio guarda sus opciones (en una columna JSON) y su respuesta correcta. La respuesta correcta nunca se envía al navegador: solo se compara en el servidor al recibir el intento, y se valida que la opción enviada sea una de las opciones del ejercicio.

**Rutas tipadas.** Laravel Wayfinder genera funciones de TypeScript a partir de las rutas de Laravel, por lo que los enlaces entre páginas (por ejemplo, del inicio a un tema) no dependen de textos escritos a mano.

**Interfaz en español.** El idioma de la app es `es`. Los textos se escriben con una función `t()` y sus traducciones viven en `lang/es.json`.

**Sistema de diseño propio.** Los colores (primario, acción `cta`, éxito, etc.) se definen como variables en `resources/css/app.css` con valores para modo claro y oscuro, y todos los componentes los reutilizan.

**Calidad automatizada.** El flujo de trabajo usa una rama por funcionalidad (`feature/...`), y el comando `composer test` ejecuta el formateo (Pint), el análisis estático (PHPStan) y las pruebas en un solo paso.

---

## Pruebas automatizadas

El proyecto cuenta con pruebas automatizadas desarrolladas con **Pest**.

Cubren:

- Inicio de sesión (casos correctos e incorrectos).
- Registro de usuarios.
- Recuperación y restablecimiento de contraseña.
- Actualización del perfil.
- Cambio de contraseña.
- Acceso protegido al dashboard.
- Cálculo del progreso individual por usuario.
- Identificación de ejercicios resueltos, temas completados y última actividad.

Aún no hay pruebas específicas para las vistas de temas y ejercicios ni para el guardado de intentos (ver "Pendiente").

Para ejecutar las verificaciones de calidad y las pruebas:

```bash
composer test
```

---

## Estructura del proyecto

```text
baldor/
├── app/
│   ├── Actions/            # Acciones de autenticación y usuarios
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── TopicController.php      # Vista de un tema
│   │   ├── ExerciseController.php   # Vista de un ejercicio y guardado de intentos
│   │   └── Settings/       # Perfil y seguridad
│   ├── Models/             # User, Topic, Exercise, Attempt
│   └── Providers/
├── database/               # Migraciones, factories y seeders
├── lang/                   # Traducciones (es.json y archivos de Laravel)
├── resources/
│   ├── css/app.css         # Colores y estilos globales
│   └── js/
│       ├── components/     # Componentes reutilizables
│       ├── layouts/        # Layouts de app, autenticación y configuración
│       ├── lib/            # Utilidades (traducción)
│       ├── pages/          # welcome, dashboard, topics/, exercises/, auth/, settings/
│       └── types/          # Tipos de TypeScript
├── routes/                 # web.php y settings.php
├── tests/                  # Pruebas con Pest
└── .github/workflows/      # Integración continua
```

### Rutas principales

| Ruta                                          | Método | Qué hace                              |
| --------------------------------------------- | ------ | ------------------------------------- |
| `/dashboard`                                  | GET    | Inicio del estudiante                 |
| `/temas/{tema}`                               | GET    | Tema con su lista de ejercicios       |
| `/temas/{tema}/ejercicios/{ejercicio}`        | GET    | Ejercicio de opción múltiple          |
| `/temas/{tema}/ejercicios/{ejercicio}/intentos` | POST | Guarda el intento y devuelve el resultado |

Todas requieren iniciar sesión y verificar el correo.

---

## Instalación local

**Requisitos:** PHP 8.3+, Composer y Node.js.

```bash
# 1. Clonar el repositorio
git clone https://github.com/Khrisstopher/baldor.git
cd baldor

# 2. Instalar y preparar la aplicación
composer setup

# 3. Cargar los datos de demostración
php artisan db:seed

# 4. Ejecutar en modo desarrollo
composer dev
```

### Usuario de demostración

Después de cargar los datos de demostración, puedes ingresar con:

- **Correo:** `test@example.com`
- **Contraseña:** `password`

Los datos de demostración incluyen temas, ejercicios con opciones de respuesta y registros de progreso para visualizar el funcionamiento del inicio, los temas y los ejercicios. Los enunciados y las opciones son de relleno hasta que se escriba el contenido real.

Abre la dirección que indique la terminal (normalmente `http://localhost:8000`) y crea tu cuenta desde la portada.

### Variables de entorno

| Variable        | Descripción                                                                                              |
| --------------- | -------------------------------------------------------------------------------------------------------- |
| `APP_NAME`      | Nombre de la app (`Baldor`)                                                                              |
| `APP_LOCALE`    | Idioma de la interfaz (`es`)                                                                             |
| `DB_CONNECTION` | Base de datos; por defecto `sqlite`                                                                      |
| `MAIL_MAILER`   | Con `log`, los correos (como el de recuperar contraseña) se guardan en `storage/logs` en vez de enviarse |

---

## Autor

**Cristofer Castro Arias**

[WhatsApp](https://wa.me/573005352422)

[YouTube](https://www.youtube.com/@KhrisstopherTube)

[Email](mailto:moralesk2026@outlook.com)

---