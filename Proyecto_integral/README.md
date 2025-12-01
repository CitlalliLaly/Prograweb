# SISTEMA DE GESTIÓN ESCOLAR CUSJ
##  Descripción

Sistema web completo para gestión escolar con soporte para múltiples roles:
- **Alumno** - Ver materias, entregar tareas, ver calificaciones
- **Profesor** - Crear actividades, calificar, gestionar inscripciones
- **Administrador** - Gestionar usuarios, departamentos, materias
- **Padre** - Monitorear calificaciones de sus hijos

##  Estructura del Proyecto

```
Proyecto_integral/
├── Includes/              # Componentes reutilizables
│   ├── conexion.php       # Conexión a BD
│   ├── header.php         # Navegación (navbar #C1FF72)
│   ├── footer.php         # Pie de página
│   └── welcome.php        # Mensaje de bienvenida
├── assets/                # Recursos estáticos
│   ├── css/style.css      # Estilos personalizados
│   ├── imgs/              # Imágenes
│   ├── profile_pictures/  # Fotos de perfil
│   └── uploads/           # Archivos subidos
├── home/
│   ├── profesor/          # Panel del profesor
│   │   ├── inscripciones.php
│   │   ├── procesar_inscripcion.php
│   │   ├── actividades.php
│   │   ├── calificar.php
│   │   └── ...
│   └── usuarios/          # Acciones del alumno
│       ├── index_alumno.php
│       ├── inscribir.php
│       └── ...
├── views/                 # Vistas por rol
│   ├── alumno/
│   ├── profesor/
│   ├── administrador/
│   └── padre/
├── sql/
│   └── control_escolar    # Dump de base de datos
├── index.php              # Landing page
├── login.php              # Autenticación
├── registro.php           # Registro de usuarios
└── README.md              # Este archivo
```

## Credenciales de Prueba

### Alumno
- Usuario: `mateos`
- Contraseña: `password` (o ver BD)

### Profesor
- Usuario: `jmartinez`
- Contraseña: `pass12`

### Administrador
- Usuario: `aramos`
- Contraseña: `password` (o ver BD)

## Características

- **Sistema de login** con múltiples roles
- **Gestión de inscripciones** - Alumno solicita, profesor aprueba/rechaza
-**Creación de actividades** por profesor
- **Calificación de tareas** - Profesor califica, alumno ve resultado
- **Panel personalizado** por rol
- **Búsqueda de cursos** - Alumno busca cursos disponibles
-**Perfil de usuario** - Información personalizada
-**Recuperación de contraseña** - Vía email
- **Navbar responsiva** con color #C1FF72

## Funcionalidades Principales

### Para Alumnos
1. Ver materias inscritas
2. Ver tareas pendientes
3. Entregar tareas
4. Ver calificaciones
5. Solicitar inscripción a nuevos cursos

### Para Profesores
1. **Gestionar inscripciones** - Aprobar/rechazar solicitudes de alumnos
2. Crear actividades (tareas, exámenes, proyectos de las materias que tiene)
3. Ver alumnos inscritos
4. Calificar actividades

### Para Administradores
1. Crear/editar usuarios tanto alumnos,padres,profesores u otros administrativos
2. Crear/editar materias
3. Crear/editar departamentos
4. Ver ranking de alumnos
5. Generar reportes

### Para Padres
1. Seleccionar hijo que deseas revisar o ver
2. Ver calificaciones
3. Monitorear progreso

### Tablas Principales
- `usuarios` - Tabla general y roles
- `alumnos` - Datos de alumnos
- `profesores` - Datos de profesores
- `cursos` - Cursos disponibles
- `materias` - Materias por departamento
- `inscripciones` - Inscripción alumno-curso
- `actividades` - Tareas, exámenes, proyectos
- `calificaciones` - Notas de alumnos
- `notificaciones` - Sistema de alertas

### Columnas Principales
- `ID_alumno` - Identificador de alumno
- `ID_curso` - Identificador de curso (MAYÚSCULA)
- `ID_profesor` - Identificador de profesor
- `ID_inscripcion` - Identificador de inscripción

## Seguridad

- Contraseñas hasheadas con bcrypt
- Validación de sesión en cada página
- Prepared statements para prevenir SQL injection
- Validación de roles antes de acciones
- Sanitización de inputs
---

**Estado**: ✅ Funcional y limpio
**Versión**: 1.0
**Última actualización**: 30-11-2025
