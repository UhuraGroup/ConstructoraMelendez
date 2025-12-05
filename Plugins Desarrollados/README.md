# 📦 Colección de Plugins Personalizados para WordPress

**Desarrollador:** Oscar Cerpa  
**Tecnologías:** PHP, jQuery, CSS3 (Flexbox/Grid), AJAX, WordPress API.

-----

## 📖 Contexto y Filosofía de Desarrollo

Este repositorio contiene una suite de soluciones a medida desarrolladas para extender las funcionalidades de WordPress sin depender de constructores visuales pesados ni excesivos plugins de terceros.

Cada plugin ha sido desarrollado bajo los siguientes pilares:

1.  **Rendimiento (Performance First):** Se prioriza el uso de CSS puro para animaciones y lógica ligera en PHP para no afectar la velocidad de carga del sitio.
2.  **Diseño "Mobile-First":** Todas las interfaces (tablas, timelines, formularios) han sido diseñadas pensando primero en la experiencia móvil y adaptándose progresivamente a escritorio.
3.  **Modularidad:** Cada carpeta funciona como un módulo independiente. Puedes instalar solo los que necesites.
4.  **Integración Nativa:** Se utilizan las funciones nativas de WordPress (Meta Boxes, Media Library) y, en casos específicos, integración con **Advanced Custom Fields (ACF)** para facilitar la administración.

-----

## 📂 Catálogo de Plugins Incluidos

A continuación se describe brevemente cada herramienta incluida en este repositorio. **Para instrucciones detalladas de instalación y edición, por favor entra en la carpeta de cada plugin y lee su respectivo `README.md`.**

### 1\. 🏠 Cotizador Inmobiliario (Versión ACF)

  * **Carpeta:** `/cotizador-inmobiliario/`
  * **Función:** Calculadora financiera para créditos hipotecarios.
  * **Tecnología Clave:** AJAX para cálculos instantáneos sin recarga.
  * **Dependencia:** Requiere **ACF Pro** activo para gestionar los precios.

### 2\. 📇 Tarjetas Interactivas B-CARD

  * **Carpeta:** `/tarjetas-interactivas/`
  * **Función:** Tarjetas informativas con efecto acordeón/expandible al pasar el cursor.
  * **Tecnología Clave:** Meta Boxes Nativos (Sin dependencias externas) y CSS Flexbox.
  * **Dependencia:** Ninguna (Standalone).

### 3\. 🏗️ Comparador de Proyectos

  * **Carpeta:** `/project-comparator/`
  * **Función:** Tabla dinámica para comparar características de hasta 3 inmuebles lado a lado.
  * **Tecnología Clave:** Transformación de Tabla a "Tarjetas" en móviles.
  * **Dependencia:** Requiere **ACF Pro** (Estructura de datos específica).

### 4\. 🕰️ Timeline Histórica

  * **Carpeta:** `/timeline-historico/`
  * **Función:** Línea de tiempo cronológica que cambia de diseño vertical (móvil) a horizontal (escritorio).
  * **Tecnología Clave:** Interacciones "Zero JS" (solo CSS) y soporte nativo SVG.
  * **Dependencia:** Ninguna (Standalone).

-----

## 🛠️ Guía General de Instalación

Aunque cada plugin tiene sus particularidades, el proceso general para implementar cualquiera de estas carpetas es:

1.  **Descargar:** Clona este repositorio o descarga la carpeta específica del plugin que necesitas.
2.  **Subir:** Sube la carpeta del plugin al directorio de tu servidor:  
    `wp-content/plugins/`
3.  **Activar:** Ve al panel de administración de WordPress \> **Plugins** y busca el nombre del plugin (ej: "Cotizador Inmobiliario") para activarlo.
4.  **Configurar:**
      * Revisa si el plugin creó un menú nuevo en la barra lateral izquierda.
      * Si el plugin requiere **ACF**, asegúrate de crear los campos personalizados tal como se indica en el README individual de esa carpeta.

-----

## 💻 Requisitos del Sistema

Para garantizar el funcionamiento correcto de todos los módulos:

  * **WordPress:** Versión 5.8 o superior.
  * **PHP:** Versión 7.4 o superior (Compatible con PHP 8.x).
  * **Advanced Custom Fields (ACF):** Requerido únicamente para el *Cotizador* y el *Comparador*.

-----

## 🤝 Soporte

Si necesitas realizar ajustes a la lógica matemática del cotizador o modificar los estilos corporativos (colores de marca), por favor consulta la sección **"Guía de Edición"** dentro del README de cada plugin antes de modificar el código fuente.
