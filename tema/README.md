# Plantilla "TDM Nova"

La carpeta `tema/` contiene la guía rápida de la plantilla visual aplicada al panel.

## Estructura
- `public/assets/theme.css`: hoja de estilos principal de la plantilla.
- Se apoya en Bootstrap 5 (desde CDN) y en la fuente "Inter".
- Los logos institucionales se cargan desde `public/uploads/` y se inyectan en el _navbar_ y los documentos exportados.

## Uso
1. Personaliza colores y componentes en `public/assets/theme.css`.
2. Sustituye o agrega logotipos desde el mantenedor de institución (Panel → Institución) para que se reflejen en la cabecera y las exportaciones.
3. Las vistas usan clases base de Bootstrap y helpers de la plantilla (`shadow-soft`, `page-heading`, `pill`), por lo que basta con editar HTML.

## Documentación
- El layout base está en `app/views/layouts/header.php` y `footer.php`.
- Los componentes visuales clave:
  - Tarjetas con borde suave (`shadow-soft`).
  - Encabezados con acento lateral (`.page-heading .accent`).
  - Badges y _chips_ (`.pill`).

Sigue esta guía para mantener coherencia visual al extender nuevas pantallas.
