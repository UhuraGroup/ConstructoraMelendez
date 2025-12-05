# 🕰️ Timeline Histórica Personalizable

**Versión:** 1.6.2  
**Autor:** [Tu Nombre / Empresa]  
**Descripción:** Sistema de línea de tiempo híbrida (Vertical/Horizontal) gestionada mediante Custom Post Types y optimizada para iconos SVG.

-----

## 📋 Descripción General

Este plugin permite crear una línea de tiempo histórica interactiva. A diferencia de otros sliders pesados, este sistema utiliza **CSS puro** para las animaciones y transiciones, garantizando una carga inmediata.

El diseño es **"Mobile-First"**:

1.  **En Móviles (\<1024px):** Se presenta como una lista vertical cronológica, con textos siempre visibles para facilitar la lectura rápida.
2.  **En Escritorio (\>1025px):** Se transforma en una línea de tiempo horizontal interactiva. Al pasar el cursor sobre un año/icono, se despliega la información.

-----

## 🌟 Características Clave

  * **Soporte Nativo para SVG:** El plugin habilita automáticamente la subida de archivos `.svg` en la biblioteca de medios (algo que WordPress bloquea por defecto) para garantizar iconos nítidos en cualquier resolución.
  * **Orden Cronológico Automático:** Los eventos se ordenan automáticamente según el campo "Año del Evento", independientemente de cuándo fueron creados en el administrador.
  * **Interacción CSS (Zero JS):** Toda la lógica de "hover" y despliegue de información en escritorio se maneja con hojas de estilo, evitando conflictos de JavaScript.
  * **Estado Activo Inteligente:** En la vista de escritorio, el **primer evento** aparece abierto por defecto para invitar a la interacción.

-----

## 🚀 Instalación y Configuración

1.  Sube la carpeta del plugin al directorio `/wp-content/plugins/`.
2.  Activa el plugin desde el panel de administración.
3.  Verás un nuevo menú llamado **Timeline Histórica**.

-----

## 💻 Guía de Uso

### 1\. Crear Eventos

Ve a **Timeline Histórica \> Añadir Nuevo**.

  * **Título:** El título del hito histórico (ej: "Fundación de la empresa").
  * **Editor de Contenido:** La descripción detallada del evento.
  * **Caja "Detalles del Evento" (Meta Box):**
      * **Año del Evento:** (Requerido) Escribe el año (ej: 1995). Este campo controla el orden de aparición.
      * **Ícono SVG:** Selecciona o sube un archivo SVG. Se mostrará una vista previa en el administrador.

### 2\. Publicar el Timeline (Shortcode)

Para mostrar la línea de tiempo, inserta este shortcode en cualquier página:

```shortcode
[timeline_historica]
```

-----

## 🎨 Personalización y Estilos (CSS)

El diseño está controlado por el archivo `css/style.css`. Utiliza **Variables CSS** para facilitar la personalización de colores sin romper la estructura.

### Variables Principales (`:root`)

Si deseas cambiar los colores corporativos, edita estas líneas al inicio del archivo CSS:

```css
:root {
    --brx-primary: #d93a3a; /* Color principal (línea, bordes, año activo) */
    --brx-dark: #2c3e50;    /* Color de textos oscuros */
    --brx-light: #ffffff;   /* Fondo de los tooltips y puntos */
    --brx-line-thickness: 4px; /* Grosor de la línea temporal */
}
```

### Comportamiento Responsivo

  * **Punto de quiebre (Breakpoint):** `1024px`.
  * **Móvil:** La clase `.brx-timeline-description` tiene `position: static` y `opacity: 1`, lo que hace que el texto siempre se vea debajo del icono.
  * **Desktop:** La descripción tiene `position: absolute`, `opacity: 0` (invisible) y `visibility: hidden`. Solo se muestra (`opacity: 1`) cuando el usuario hace `:hover` sobre el evento (`.brx-timeline-event:hover`).

-----

## ⚙️ Notas para Desarrolladores

### Estructura de Archivos

```text
timeline-historica/
├── css/
│   └── style.css            # Contiene toda la lógica visual y responsive
├── timeline-historica.php   # Lógica PHP (CPT, MetaBoxes, Shortcode)
└── README.md                # Documentación
```

### Lógica del "Evento Activo"

En `timeline-historica.php`, el bucle `while` detecta el primer elemento:

```php
if ($is_first_event) {
    $event_classes .= ' evento-activo';
    // ...
}
```

Esto permite que, mediante CSS, el primer ítem siempre esté visible en Desktop hasta que el usuario interactúe con otro elemento, mejorando la UX.

### Manejo de SVGs

El plugin utiliza el filtro `upload_mimes` para permitir SVGs y el filtro `wp_prepare_attachment_for_js` para corregir un bug visual común de WordPress donde los SVGs no muestran miniatura en la biblioteca de medios.

-----

## ⚠️ Solución de Problemas Frecuentes

  * **Los eventos no salen en orden:** Asegúrate de haber llenado el campo "Año del Evento". El plugin ordena numéricamente por este campo, no por fecha de publicación.
  * **No puedo subir iconos SVG:** Si tienes otro plugin de seguridad (como Wordfence o iThemes), verifica que no estén bloqueando la subida de archivos XML/SVG.
  * **El texto se corta en móviles:** El diseño móvil apila los elementos. Si tienes descripciones excesivamente largas, considera usar extractos más breves para mantener la estética.
