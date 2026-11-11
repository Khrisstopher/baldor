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

Baldor está en construcción. Hoy ya funciona toda la parte de cuentas de usuario y la interfaz principal, pero **el contenido de álgebra (temas y ejercicios) todavía no existe**: el inicio usa datos de ejemplo mientras se diseña la base de datos.

> Nota sobre el contenido: este repositorio no incluye material del libro. Los temas y ejercicios que se agreguen serán propios, inspirados en el estilo de enseñanza de Baldor.

**Hecho**

- [x] Portada con descripción de la app
- [x] Registro, inicio y cierre de sesión
- [x] Recuperación de contraseña (rutas y pantallas listas)
- [x] Configuración de cuenta: perfil, seguridad y apariencia (modo claro/oscuro)
- [x] Interfaz completamente en español
- [x] Inicio del estudiante con tres bloques: continuar donde lo dejó, progreso general y temas (con datos de ejemplo)
- [x] Pruebas automatizadas y análisis estático

**Pendiente**

- [ ] Modelo de datos: temas, ejercicios y progreso por usuario
- [ ] Conectar el inicio a la base de datos
- [ ] Vista de un tema con su lista de ejercicios
- [ ] Resolver ejercicios y guardar el avance
- [ ] Envío real de correos para recuperar la contraseña
- [ ] Logo e imágenes definitivas
- [ ] Página "Acerca de"

---

## ¿Qué hace esta app?

Los usuarios pueden registrarse, iniciar sesión y entrar a su **inicio**, que muestra:

1. **Continuar donde lo dejaste:** acceso directo al último tema o ejercicio (o una invitación a empezar por el primero si es un usuario nuevo).
2. **Progreso general:** barra de porcentaje, ejercicios resueltos y temas completados.
3. **Temas:** tarjetas con el estado de cada tema (sin empezar, en curso o completado) y su avance.

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

**Interfaz en español.** El idioma de la app es `es`. Los textos se escriben con una función `t()` y sus traducciones viven en `lang/es.json`.

**Sistema de diseño propio.** Los colores (primario, acción `cta`, éxito, etc.) se definen como variables en `resources/css/app.css` con valores para modo claro y oscuro, y todos los componentes los reutilizan.

**Calidad automatizada.** El flujo de trabajo usa una rama por funcionalidad (`feature/...`), y el comando `composer test` ejecuta el formateo (Pint), el análisis estático (PHPStan) y las pruebas en un solo paso.

---

## Pruebas automatizadas

Hay 26 pruebas con **Pest** que cubren:

- Inicio de sesión (casos correctos e incorrectos)
- Registro de usuarios
- Recuperación y restablecimiento de contraseña
- Actualización de perfil
- Cambio de contraseña en seguridad
- Acceso protegido al inicio

```bash
composer test
```

---

## Estructura del proyecto

```
baldor/
├── app/
│   ├── Actions/            # Acciones de autenticación y usuarios
│   ├── Http/Controllers/
│   │   └── Settings/       # Perfil y seguridad
│   ├── Models/             # User
│   └── Providers/
├── lang/                   # Traducciones (es.json y archivos de Laravel)
├── resources/
│   ├── css/app.css         # Colores y estilos globales
│   └── js/
│       ├── components/     # Componentes reutilizables
│       ├── layouts/        # Layouts de app, autenticación y configuración
│       ├── lib/            # Utilidades (traducción)
│       └── pages/          # welcome, dashboard, auth/*, settings/*
├── routes/                 # web.php y settings.php
├── tests/                  # Pruebas con Pest
└── .github/workflows/      # Integración continua
```

---

## Instalación local

Requisitos: **PHP 8.3+**, **Composer** y **Node.js**.

```bash
# 1. Clonar el repositorio
git clone https://github.com/Khrisstopher/baldor.git
cd baldor

# 2. Instalar y preparar todo
#    (dependencias, .env, clave de la app, migraciones y build)
composer setup

# 3. Ejecutar en modo desarrollo
composer dev
```

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
