# 🏗️ Comparador de Proyectos ACF

**Versión:** 1.1.0  
**Autor:** Oscar Cerpa  
**Descripción:** Sistema de comparación dinámica de inmuebles que permite confrontar hasta 3 proyectos simultáneamente sin recargar la página.

-----

## 📋 Descripción General

Este plugin habilita una tabla comparativa interactiva mediante el shortcode `[project_comparator]`. Al cargar la página, el sistema obtiene los datos de todos los proyectos publicados y los sirve al navegador. Mediante JavaScript, la tabla se actualiza instantáneamente cuando el usuario selecciona una opción en los menús desplegables.

### 🌟 Características Principales

  * **Interacción Instantánea:** No requiere peticiones AJAX al servidor al cambiar de proyecto; los datos están precargados para una velocidad máxima.
  * **Diseño Responsive Avanzado:** En escritorio se muestra como una tabla clásica. En móviles (\<768px), se transforma en una vista de "tarjetas" verticales para facilitar la lectura.
  * **Integración Nativa con ACF:** Se alimenta directamente de los campos personalizados de tus Custom Post Types.

-----

## ⚙️ Requisitos y Configuración de Datos

Para que el comparador funcione, tu instalación de WordPress debe cumplir estrictamente con la siguiente estructura de datos:

### 1\. Custom Post Type (CPT)

El plugin busca posts del tipo: `proyecto`.

### 2\. Campos de Advanced Custom Fields (ACF)

Cada proyecto debe tener los siguientes campos configurados. **Es vital que los "Field Names" (nombres de campo) coincidan exactamente:**

| Etiqueta (Label) | Nombre del Campo (Name) | Tipo de Campo ACF | Notas |
| :--- | :--- | :--- | :--- |
| **Logo del Proyecto** | `logo` | Imagen | Devuelve Array o URL (El código espera Array). |
| **Precio (COP)** | `precio_cop` | Número | Solo números, sin puntos ni comas. |
| **Tipo de Inmueble** | `tipo_de_inmueble` | Texto / Select | Ej: Apartamento, Casa. |
| **Grupo de Áreas** | `areas_del_inmueble` | Grupo (Group) | **Contenedor principal.** |
| ↳ Área Construida | `area_total_construida` | Número / Texto | Dentro del grupo anterior. |
| ↳ Área Privada | `area_privada` | Número / Texto | Dentro del grupo anterior. |

-----

## 🚀 Instalación y Uso

1.  **Instalación:** Sube la carpeta del plugin al directorio `/wp-content/plugins/` y actívalo.
2.  **Implementación:** Coloca el siguiente shortcode en cualquier página o entrada:

<!-- end list -->

```shortcode
[project_comparator]
```

-----

## 📂 Guía de Edición para Desarrolladores

Si necesitas modificar la lógica o el diseño, aquí tienes la guía de los archivos incluidos:

### 1\. Lógica del Servidor (`project-comparator.php`)

Este archivo maneja la obtención de datos y la estructura HTML inicial.

  * **Cambiar el CPT:** Si tu tipo de post no se llama `proyecto`, busca la línea `$args = array('post_type' => 'proyecto' ...` y cámbialo.
  * **Añadir nuevas filas a la tabla:**
    1.  En la función `project_comparator_enqueue_assets`, añade el nuevo campo al array `$all_projects_data`.
    2.  En `project_comparator_shortcode_callback`, añade la fila HTML `<tr>` con un ID único (ej: `id="comp-nuevodato-1"`).
  * **Scripts:** Usa `wp_localize_script` para pasar los datos de PHP a JS en la variable global `projectData`.

### 2\. Lógica del Cliente (`comparator-scripts.js`)

Maneja la actualización del DOM.

  * **Función `updateComparisonColumn`:** Aquí es donde se "pintan" los datos en la tabla. Si añadiste un campo nuevo en el PHP, debes añadir la línea correspondiente aquí:
    ```javascript
    // Ejemplo para añadir un campo nuevo
    $('#comp-nuevodato-' + column).html(selectedProject.nuevoDato);
    ```
  * **UX Móvil:** El script detecta el ancho de la pantalla y cambia el texto de los selectores ("Selecciona un proyecto" vs "Selecciona") para ahorrar espacio en móviles.

### 3\. Estilos (`comparator-styles.css`)

Diseño visual basado en la fuente 'Manrope'.

  * **Responsive:** Presta atención a la media query `@media (max-width: 768px)`. Aquí ocurre la "magia" donde la `table`, `thead`, `tbody`, `tr`, `td` cambian su `display` a `block` para apilarse verticalmente.
  * **Bordes:** Se aplican `border-radius: 10px` a los selectores y contenedores de logos para un look moderno.

-----

## ⚠️ Solución de Problemas

  * **La tabla aparece vacía:** Verifica que los proyectos estén publicados y no en borrador.
  * **Faltan datos (como el área):** Asegúrate de que en ACF el campo `area_total_construida` esté **dentro** del grupo `areas_del_inmueble`. Si no usas un grupo, deberás editar `project-comparator.php` para sacar los campos del array `$grupo_areas`.
  * **No se ven las imágenes:** Revisa que el campo `logo` en ACF esté configurado para devolver un "Array de imagen" (Image Array), ya que el código usa `$logo['url']`.

-----

## 📝 Estructura de Archivos

```text
project-comparator/
├── comparator-scripts.js   # Lógica JS (Interacción y DOM)
├── comparator-styles.css   # Estilos CSS (Responsive y Tabla)
├── placeholder.png         # Imagen por defecto si falta el logo
├── project-comparator.php  # Archivo principal del plugin
└── README.md               # Documentación
```
