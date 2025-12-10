# Sistema oficial de campeonatos de Tenis de Mesa

Aplicación web en PHP MVC para gestionar campeonatos de tenis de mesa conforme a ITTF y federación chilena.

## Requisitos
- PHP 8+
- MySQL/MariaDB
- Servidor web apuntando a `public/`

## Instalación
1. Crear base de datos y ejecutar `database.sql`:
   ```bash
   mysql -u root -p tdm < database.sql
   ```
2. Ajustar credenciales en `config/database.php` o variables de entorno `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.
3. Servir la carpeta `public/` (por ejemplo con PHP built-in):
   ```bash
   php -S localhost:8000 -t public
   ```

Usuario administrador preconfigurado: `admin@tdm.local` / `admin123`.

## Características principales
- Autenticación con roles (administrador, árbitro, jugador)
- Inscripción pública de jugadores con datos federativos
- Gestión de rankings para sembrados oficiales
- Creación de campeonatos con formato grupos + eliminación
- Generación automática de grupos (serpentina) y partidos
- Registro de resultados con validación ITTF (sets a 11 y diferencia mínima de 2)
- Dashboard con métricas rápidas de estado
- Libro oficial exportable a PDF y Excel con logo institucional
- Mantenedor de institución (datos de contacto y logotipo para vistas y documentos)

## Estructura
```
app/
 controllers/
 models/
 views/
config/
public/
tema/
```
