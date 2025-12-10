-- Schema de gestión de campeonatos de tenis de mesa
-- Ejecutar este script en MySQL/MariaDB (utf8mb4) para preparar todas las tablas base.

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO roles (nombre, descripcion) VALUES
    ('Visitante', 'Usuario con permisos de solo lectura'),
    ('Arbitro', 'Control de partidos y reportes de juego'),
    ('Jugador', 'Acceso a inscripciones y seguimiento de resultados'),
    ('Administrador', 'Control total del sistema')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol_id INT UNSIGNED NOT NULL,
    telefono VARCHAR(30) NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO usuarios (nombre, email, password_hash, rol_id, telefono, activo)
SELECT 'Super Administrador', 'eisla@local.test', '$2y$12$V3vU5mR2SQeE/ABIBPyb/e.qcyA/p1WuelpOGEirv4lay4AzTphB6', r.id, NULL, 1
FROM roles r
WHERE r.nombre = 'Administrador'
ON DUPLICATE KEY UPDATE
    nombre = VALUES(nombre),
    password_hash = VALUES(password_hash),
    rol_id = VALUES(rol_id),
    activo = 1;

CREATE TABLE IF NOT EXISTS clubes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL UNIQUE,
    ciudad VARCHAR(120) NULL,
    pais VARCHAR(120) NULL,
    contacto VARCHAR(120) NULL,
    telefono VARCHAR(30) NULL,
    email VARCHAR(120) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sedes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    direccion VARCHAR(255) NULL,
    ciudad VARCHAR(120) NULL,
    pais VARCHAR(120) NULL,
    contacto VARCHAR(120) NULL,
    telefono VARCHAR(30) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY ux_sedes_nombre_ciudad (nombre, ciudad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS mesas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sede_id INT UNSIGNED NOT NULL,
    codigo VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255) NULL,
    estado ENUM('disponible','en_mantenimiento','reservada') NOT NULL DEFAULT 'disponible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY ux_mesas_sede_codigo (sede_id, codigo),
    FOREIGN KEY (sede_id) REFERENCES sedes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS arbitros (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    email VARCHAR(120) NULL UNIQUE,
    telefono VARCHAR(30) NULL,
    nivel VARCHAR(80) NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS jugadores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    club_id INT UNSIGNED NULL,
    nombres VARCHAR(120) NOT NULL,
    apellidos VARCHAR(120) NOT NULL,
    documento VARCHAR(50) NULL UNIQUE,
    genero ENUM('M','F','O') NULL,
    fecha_nacimiento DATE NULL,
    mano_preferida ENUM('derecha','izquierda') NULL,
    ranking_nacional INT UNSIGNED NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (club_id) REFERENCES clubes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS campeonatos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL UNIQUE,
    categoria VARCHAR(100) NULL,
    modalidad ENUM('individual','dobles','equipos') NOT NULL DEFAULT 'individual',
    sede_id INT UNSIGNED NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    estado ENUM('borrador','inscripciones','programado','en_juego','finalizado') NOT NULL DEFAULT 'borrador',
    reglamento_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sede_id) REFERENCES sedes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS equipos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campeonato_id INT UNSIGNED NOT NULL,
    club_id INT UNSIGNED NULL,
    nombre VARCHAR(120) NOT NULL,
    categoria VARCHAR(100) NULL,
    entrenador VARCHAR(120) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY ux_equipos_campeonato_nombre (campeonato_id, nombre),
    FOREIGN KEY (campeonato_id) REFERENCES campeonatos(id),
    FOREIGN KEY (club_id) REFERENCES clubes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS inscripciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campeonato_id INT UNSIGNED NOT NULL,
    jugador_id INT UNSIGNED NULL,
    equipo_id INT UNSIGNED NULL,
    modalidad ENUM('individual','dobles','equipos') NOT NULL,
    rol ENUM('principal','suplente') NOT NULL DEFAULT 'principal',
    estado ENUM('pendiente','aprobada','rechazada','pagada') NOT NULL DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHECK ((modalidad = 'equipos' AND equipo_id IS NOT NULL) OR (modalidad <> 'equipos' AND jugador_id IS NOT NULL)),
    FOREIGN KEY (campeonato_id) REFERENCES campeonatos(id),
    FOREIGN KEY (jugador_id) REFERENCES jugadores(id),
    FOREIGN KEY (equipo_id) REFERENCES equipos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS partidos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campeonato_id INT UNSIGNED NOT NULL,
    fase VARCHAR(120) NULL,
    ronda VARCHAR(120) NULL,
    tipo ENUM('individual','dobles','equipos') NOT NULL DEFAULT 'individual',
    fecha_programada DATE NULL,
    hora_programada TIME NULL,
    sede_id INT UNSIGNED NULL,
    mesa_id INT UNSIGNED NULL,
    arbitro_id INT UNSIGNED NULL,
    estado ENUM('programado','en_juego','finalizado','suspendido','cancelado') NOT NULL DEFAULT 'programado',
    marcador_local TINYINT UNSIGNED NULL,
    marcador_visitante TINYINT UNSIGNED NULL,
    ganador_tipo ENUM('jugador','equipo') NULL,
    ganador_id INT UNSIGNED NULL,
    observaciones TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (campeonato_id) REFERENCES campeonatos(id),
    FOREIGN KEY (sede_id) REFERENCES sedes(id),
    FOREIGN KEY (mesa_id) REFERENCES mesas(id),
    FOREIGN KEY (arbitro_id) REFERENCES arbitros(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS partido_participantes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    partido_id INT UNSIGNED NOT NULL,
    participante_tipo ENUM('jugador','equipo') NOT NULL,
    participante_id INT UNSIGNED NOT NULL,
    es_local TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY ux_partido_participante (partido_id, participante_tipo, participante_id),
    FOREIGN KEY (partido_id) REFERENCES partidos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS partido_sets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    partido_id INT UNSIGNED NOT NULL,
    numero_set TINYINT UNSIGNED NOT NULL,
    puntaje_local TINYINT UNSIGNED NOT NULL,
    puntaje_visitante TINYINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY ux_partido_set (partido_id, numero_set),
    FOREIGN KEY (partido_id) REFERENCES partidos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS rankings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campeonato_id INT UNSIGNED NOT NULL,
    jugador_id INT UNSIGNED NOT NULL,
    posicion INT UNSIGNED NOT NULL,
    puntos INT NOT NULL DEFAULT 0,
    categoria VARCHAR(100) NULL,
    fecha_actualizacion DATE NOT NULL,
    UNIQUE KEY ux_ranking_campeonato_jugador (campeonato_id, jugador_id),
    FOREIGN KEY (campeonato_id) REFERENCES campeonatos(id),
    FOREIGN KEY (jugador_id) REFERENCES jugadores(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS historico_resultados (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    jugador_id INT UNSIGNED NOT NULL,
    campeonato_id INT UNSIGNED NOT NULL,
    partido_id INT UNSIGNED NOT NULL,
    ronda VARCHAR(120) NULL,
    resultado ENUM('ganado','perdido','retirado','walkover') NOT NULL,
    puntos_otorgados INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (jugador_id) REFERENCES jugadores(id),
    FOREIGN KEY (campeonato_id) REFERENCES campeonatos(id),
    FOREIGN KEY (partido_id) REFERENCES partidos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pagos_inscripciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inscripcion_id INT UNSIGNED NOT NULL,
    monto DECIMAL(10,2) NOT NULL DEFAULT 0,
    moneda CHAR(3) NOT NULL DEFAULT 'USD',
    fecha_pago DATETIME NOT NULL,
    referencia VARCHAR(120) NULL,
    estado ENUM('pendiente','confirmado','fallido') NOT NULL DEFAULT 'confirmado',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inscripcion_id) REFERENCES inscripciones(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Vistas sugeridas (opcional) para reportes rápidos
-- CREATE OR REPLACE VIEW vista_partidos_programados AS
-- SELECT p.id, c.nombre AS campeonato, p.fase, p.ronda, p.fecha_programada, p.hora_programada,
--        s.nombre AS sede, m.codigo AS mesa, a.nombre AS arbitro, p.estado
-- FROM partidos p
-- LEFT JOIN campeonatos c ON p.campeonato_id = c.id
-- LEFT JOIN sedes s ON p.sede_id = s.id
-- LEFT JOIN mesas m ON p.mesa_id = m.id
-- LEFT JOIN arbitros a ON p.arbitro_id = a.id;

-- Nuevos módulos locales
CREATE TABLE IF NOT EXISTS cuadros_campeonatos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campeonato_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    tipo ENUM('Eliminatoria','Fase de grupos','Mixto') NOT NULL DEFAULT 'Eliminatoria',
    fase_inicial VARCHAR(50) NULL,
    detalle TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campeonato_id) REFERENCES campeonatos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS programacion_manual (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campeonato_id INT UNSIGNED NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NULL,
    mesa VARCHAR(50) NULL,
    fase VARCHAR(80) NULL,
    detalle TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campeonato_id) REFERENCES campeonatos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS resultados_manual (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    partido_id INT UNSIGNED NOT NULL,
    ganador_jugador_id INT UNSIGNED NULL,
    marcador VARCHAR(50) NULL,
    detalle TEXT NULL,
    fecha DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (partido_id) REFERENCES partidos(id),
    FOREIGN KEY (ganador_jugador_id) REFERENCES jugadores(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Administración y logística
CREATE TABLE IF NOT EXISTS sedes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    ciudad VARCHAR(100) NOT NULL,
    direccion VARCHAR(200) NULL,
    capacidad VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS mesas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sede_id INT UNSIGNED NOT NULL,
    codigo VARCHAR(50) NOT NULL,
    descripcion VARCHAR(150) NULL,
    FOREIGN KEY (sede_id) REFERENCES sedes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS arbitros (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    rol VARCHAR(50) NOT NULL,
    licencia VARCHAR(100) NULL,
    telefono VARCHAR(50) NULL,
    mesa_asignada VARCHAR(50) NULL,
    disponibilidad TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS configuraciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT NOT NULL,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
