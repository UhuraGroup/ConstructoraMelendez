# 🏠 Cotizador Inmobiliario (Versión ACF)

**Versión:** 2.1  
**Autor:** Oscar Cerpa  
**Descripción:** Plugin de WordPress para calcular cuotas hipotecarias estimadas basado en proyectos inmobiliarios.

-----

## 📋 Descripción General

Este plugin genera un formulario interactivo que permite a los usuarios seleccionar un proyecto inmobiliario, una moneda (COP, USD, EUR) y un porcentaje de cuota inicial para recibir una estimación financiera instantánea.

El sistema utiliza **AJAX** para realizar los cálculos sin recargar la página y depende de **Advanced Custom Fields (ACF)** para gestionar los precios de los proyectos.

-----

## ⚙️ Requisitos Previos

Para que este plugin funcione correctamente, el entorno de WordPress debe cumplir con lo siguiente:

1.  **Plugin Advanced Custom Fields (ACF) Activo:** El plugin utiliza `get_field()` para obtener los precios.
2.  **Custom Post Type (CPT) 'proyecto':** Debe existir un tipo de post llamado `proyecto`.
3.  **Campos Personalizados:** Cada proyecto debe tener los siguientes campos de número en ACF:
      * `precio_cop` (Precio en Pesos Colombianos)
      * `precio_usd` (Precio en Dólares)
      * `precio_eur` (Precio en Euros)

-----

## 🚀 Instalación y Configuración

1.  Sube la carpeta del plugin al directorio `/wp-content/plugins/`.
2.  Activa el plugin desde el panel de administración de WordPress.
3.  Ve a **Ajustes \> Cotizador Inmobiliario**.
4.  Configura las variables globales de financiación:
      * **Plazo del crédito (meses):** (Ej: 180 para 15 años).
      * **Tasa de Interés Anual (%):** (Ej: 10.5 para una tasa del 10.5% E.A.).

-----

## 💻 Uso (Shortcode)

Para mostrar el cotizador en cualquier página, entrada o widget, utiliza el siguiente shortcode:

```shortcode
[cotizador_inmobiliario]
```

-----

## 📂 Guía de Edición de Archivos

A continuación se detalla la estructura del plugin y cómo editar cada archivo para realizar cambios específicos.

### 1\. Estilos Visuales (`assets/css/cotizador-styles.css`)

Este archivo controla la apariencia del formulario y los resultados.

  * **Cambiar colores:** Busca la clase `.cotizador-container` para el fondo principal o `#calcular-btn` para el color del botón (actualmente `#E20E17`).
  * **Ajustar diseño:** El formulario usa `display: grid`. Si deseas cambiar cuántas columnas se ven en móviles o escritorio, edita `.cotizador-form`.
  * **Tipografía:** La fuente está definida en `.cotizador-container`. Puedes cambiarla para que coincida con el tema del sitio.

### 2\. Lógica Frontend (`assets/js/cotizador-logic.js`)

Este archivo maneja la interacción del usuario, validaciones y la comunicación con el servidor.

  * **Formato de Moneda:** La función `formatCurrency` utiliza `Intl.NumberFormat('es-CO')`. Si deseas cambiar el formato de visualización (ej. decimales), edita esta función.
  * **Textos de carga:** Puedes cambiar el comportamiento del loader o las alertas de error dentro de la llamada `$.ajax`.
  * **Eventos:** Aquí se detecta el cambio de selectores (`change`) y el clic en calcular (`click`).

### 3\. Lógica del Servidor (`cotizador-inmobiliario.php`)

Este es el núcleo del plugin. Contiene el registro del plugin, el shortcode y la fórmula matemática financiera.

  * **Añadir nuevos campos al formulario:** Debes editar la función `ci_shortcode_html`.
  * **Modificar la fórmula financiera:** Busca la función `ci_handle_calculation`. Actualmente usa la fórmula de amortización francesa (cuota fija):
    ```php
    $cuota_fija = ($monto_financiado * $tasa_mensual * pow(1 + $tasa_mensual, $plazo_meses)) / (pow(1 + $tasa_mensual, $plazo_meses) - 1);
    ```
  * **Cambiar configuración predeterminada:** Los valores por defecto (como 180 meses o 10.5%) se definen en `ci_settings_init` y se recuperan en `ci_handle_calculation`.

-----

## ⚠️ Solución de Problemas Frecuentes

  * **El cotizador muestra $0 en todos los campos:**
      * Verifica que el proyecto seleccionado tenga llenos los campos `precio_cop`, `precio_usd`, etc., en el administrador de WordPress.
      * Asegúrate de que ACF esté activo.
  * **Error "ACF no está activo":** El plugin se desactivará o mostrará un mensaje de error si no detecta la función `get_field()`.
  * **El botón calcular no hace nada:** Abre la consola del navegador (F12) y verifica si hay errores de JavaScript. Asegúrate de que jQuery esté cargado en tu tema.

-----

## 📝 Estructura de Carpetas Recomendada

```text
cotizador-inmobiliario/
├── assets/
│   ├── css/
│   │   └── cotizador-styles.css
│   └── js/
│       └── cotizador-logic.js
├── cotizador-inmobiliario.php
└── README.md
```
