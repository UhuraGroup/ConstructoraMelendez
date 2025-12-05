# 🏗️ Suite de Plugins - Constructora Meléndez

**Desarrollador:** Oscar Cerpa 
**Cliente:** Constructora Meléndez S.A.  
**Sector:** Real Estate / Construcción e Inmobiliaria  
**Tecnologías:** WordPress, PHP 7.4+, ACF Pro, jQuery, CSS3.

---

## 📖 Descripción del Proyecto

Este repositorio aloja una colección de plugins personalizados desarrollados para el ecosistema digital de **Constructora Meléndez**, una compañía líder con más de 60 años de trayectoria en la transformación urbana del Valle del Cauca.

El objetivo de estas herramientas es optimizar la experiencia de usuario (UX) en el sitio web corporativo, facilitando la toma de decisiones de compra de vivienda mediante herramientas financieras interactivas y visualización dinámica de datos.

### 🎯 Objetivos Técnicos
* **Modularidad:** Cada funcionalidad es un plugin independiente, evitando la dependencia de temas pesados ("bloatware").
* **Performance:** Uso de CSS puro y JavaScript nativo/jQuery optimizado para mantener una carga rápida en dispositivos móviles.
* **Integración de Datos:** Conexión directa con campos personalizados (ACF) para gestionar precios, áreas y características de los inmuebles desde el administrador.

---

## 🛠️ Stack Tecnológico

El desarrollo está optimizado para el siguiente entorno de producción:

* **CMS:** WordPress 6.x
* **Lenguaje:** PHP 7.4 o superior (Compatible con PHP 8.x).
* **Gestión de Datos:** Advanced Custom Fields (ACF) PRO.
* **Frontend:** HTML5, CSS3 (Variables CSS, Flexbox/Grid), jQuery.

---

## 📂 Módulos Incluidos

A continuación se describen las soluciones incluidas en este repositorio. Para detalles de instalación y edición, consulte el `README.md` dentro de cada carpeta.

### 1. 🏠 Cotizador Inmobiliario
* **Propósito:** Permite a los usuarios estimar cuotas hipotecarias y financiación para proyectos (VIS y No VIS).
* **Key Feature:** Cálculo en tiempo real vía AJAX sin recargar la página y soporte multidivisa (COP, USD, EUR) para compradores en el exterior.
* **Ruta:** `/cotizador-inmobiliario/`

### 2. 🏗️ Comparador de Proyectos
* **Propósito:** Herramienta de soporte a la decisión que permite confrontar características (Área privada, Precio, Ubicación) de hasta 3 inmuebles simultáneamente.
* **Key Feature:** Diseño adaptativo que transforma la tabla comparativa en "tarjetas" verticales en dispositivos móviles para mejorar la legibilidad.
* **Ruta:** `/project-comparator/`

### 3. 🕰️ Timeline Histórica Corporativa
* **Propósito:** Visualización interactiva del legado de más de 60 años de la constructora (Hitos como Ciudad Jardín, Unicentro, etc.).
* **Key Feature:** Animaciones fluidas utilizando únicamente CSS (Zero-JS en frontend) y soporte nativo para iconos SVG.
* **Ruta:** `/timeline-historica/`

### 4. 📇 Tarjetas Interactivas (B-CARD)
* **Propósito:** Componentes UI modernos para destacar características de sostenibilidad (Sello EDGE) o beneficios de proyectos.
* **Key Feature:** Efecto "acordeón" expansible al pasar el cursor, gestionado mediante Meta Boxes nativos de WordPress.
* **Ruta:** `/tarjetas-interactivas/`

---

## 🚀 Instalación General

1.  Clone este repositorio en su entorno local o servidor de desarrollo.
2.  Copie la carpeta del plugin deseado al directorio `/wp-content/plugins/` de su instalación de WordPress.
3.  Active el plugin desde el panel de administración.
4.  **Importante:** Verifique que el plugin **Advanced Custom Fields PRO** esté activo para el funcionamiento correcto de los módulos de *Cotización* y *Comparación*.

---

## 📄 Licencia y Uso

Este código es propiedad de **Constructora Meléndez** y su uso está restringido al desarrollo y mantenimiento de sus plataformas digitales.

Desarrollado con ❤️ y código limpio por **Oscar Cerpa**.
