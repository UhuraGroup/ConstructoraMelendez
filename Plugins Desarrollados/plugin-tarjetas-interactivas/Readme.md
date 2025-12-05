# 📇 Tarjetas Interactivas B-CARD (Independiente)

**Versión:** 2.0  
**Autor:** Oscar Cerpa  
**Descripción:** Plugin ligero para mostrar tarjetas informativas con efecto de acordeón interactivo (expandibles al pasar el mouse).

-----

## 📋 Descripción General

Este plugin permite crear y gestionar "Tarjetas Interactivas" desde el panel de administración de WordPress sin depender de plugins externos como ACF.

Al pasar el cursor sobre las tarjetas, estas se expanden suavemente utilizando animaciones CSS (Flexbox) para revelar contenido adicional, creando una experiencia de usuario dinámica y moderna.

### 🌟 Características Principales

  * **Zero Dependencias:** No requiere ACF ni otros plugins. Utiliza Meta Boxes nativos de WordPress.
  * **Gestor de Medios Nativo:** Integración completa con la biblioteca de medios de WordPress para subir imágenes de fondo.
  * **Dos Tipos de Tarjeta:**
      * **Expandible (Abierta):** Muestra título, descripción y botón al interactuar.
      * **Compacta (Cerrada):** Muestra título e icono, ideal para tarjetas secundarias.
  * **Responsive:** Se adapta automáticamente a dispositivos móviles apilando las tarjetas verticalmente.

-----

## 🚀 Instalación

1.  Sube la carpeta del plugin al directorio `/wp-content/plugins/` de tu instalación de WordPress.
2.  Activa el plugin desde el menú **Plugins** en el administrador.
3.  Verás un nuevos menú llamado **Tarjetas Interactivas** en la barra lateral izquierda.

-----

## 💻 Guía de Uso

### 1\. Crear Tarjetas

1.  Ve a **Tarjetas Interactivas \> Añadir nueva**.
2.  Ingresa el **Título** (aparecerá en la cabecera de la tarjeta).
3.  En el editor de texto principal, escribe la descripción (solo visible en tarjetas tipo "Expandible").
4.  En la caja **"Datos de la Tarjeta Interactiva"** (debajo del editor):
      * **Texto del Botón:** Ej: "Ver Más".
      * **URL del Botón:** El enlace de destino.
      * **Tipo de Tarjeta:** Elige entre *Expandible* o *Compacta*.
      * **Imagen de Fondo:** Selecciona una imagen de tu biblioteca.

### 2\. Mostrar las Tarjetas (Shortcode)

Para mostrar el contenedor con las tarjetas (máximo 3 por defecto), usa el siguiente shortcode en cualquier página o entrada:

```shortcode
[tarjetas_interactivas]
```

-----

## 📂 Guía de Edición para Desarrolladores

A continuación se detalla la función de cada archivo para facilitar futuras modificaciones o mantenimiento.

### 1\. Núcleo del Plugin (`plugin-tarjetas-interactivas.php`)

Este archivo controla toda la lógica PHP.

  * **Registro del CPT:** Si deseas cambiar el nombre del menú o el icono, busca la función `bcard_register_post_type`.
  * **Campos Personalizados (Meta Boxes):** A diferencia de ACF, los campos se crean manualmente en la función `bcard_add_meta_box` y se guardan en `bcard_save_postdata`. Si necesitas agregar un campo extra (ej. un subtítulo), debes editar estas dos funciones y el HTML en `bcard_metabox_html_callback`.
  * **Shortcode:** La función `bcard_display_shortcode` genera el HTML que ve el usuario final.

### 2\. Estilos Visuales (`css/bcard-styles.css`)

Controla la apariencia y las animaciones.

  * **Animación de Acordeón:** Se maneja con las propiedades `flex`.
      * `.bcard--cerrada { flex: 1; }`
      * `.bcard:hover { flex: 4; }` (Cambia este valor para que la tarjeta crezca más o menos).
  * **Colores:**
      * **Overlay (Filtro Oscuro):** `.bcard-overlay` (Actualmente negro con transparencia).
      * **Color al pasar el mouse:** `.bcard:hover .bcard-overlay` (Actualmente rojo: `rgba(200, 40, 40, 0.75)`). **Edita esto si deseas cambiar el color de marca.**
  * **Altura:** La altura del contenedor está en `.bcard-container` (`min-height: 380px`).

### 3\. Javascript de Administración (`admin/bcard-admin.js`)

Este archivo **solo se carga en el panel de administración**.

  * Controla el botón "Seleccionar Imagen". Abre la librería multimedia nativa de WordPress y devuelve el ID y la URL de la imagen seleccionada al campo oculto del Meta Box. No suele requerir edición a menos que cambies la lógica de subida de imágenes.

### 4\. Javascript Frontend (`js/bcard-scripts.js`)

  * Actualmente es un archivo base. Si en el futuro deseas agregar interactividad avanzada (como analíticas al hacer clic o efectos de sonido), este es el lugar correcto.

-----

## ⚠️ Notas Técnicas

  * **Orden de las tarjetas:** Las tarjetas se muestran ordenadas por el atributo "Orden" (Menu Order) de la página de edición, de forma ascendente.
  * **Imágenes:** Se recomienda usar imágenes optimizadas (aprox. 800x600px) para no afectar la velocidad de carga, ya que se usan como `background-image`.
  * **Seguridad:** El plugin implementa `wp_nonce_field` para proteger el guardado de datos en los Meta Boxes.

-----

## 📝 Estructura de Carpetas

```text
tarjetas-interactivas-bcard/
├── admin/
│   └── bcard-admin.js        # Lógica para el uploader de medios en WP Admin
├── css/
│   └── bcard-styles.css      # Estilos CSS del frontend
├── js/
│   └── bcard-scripts.js      # Scripts JS del frontend
├── plugin-tarjetas-interactivas.php  # Archivo principal
└── README.md                 # Documentación
```
