CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','referee','player') DEFAULT 'admin'
);

CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    document VARCHAR(50) NOT NULL,
    birthdate DATE NOT NULL,
    club VARCHAR(120) NOT NULL,
    category VARCHAR(50) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    email VARCHAR(120) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    ranking INT NULL
);

CREATE TABLE championships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    location VARCHAR(120) NOT NULL,
    start_date DATE NOT NULL,
    category VARCHAR(50) NOT NULL,
    modality VARCHAR(50) NOT NULL,
    participants INT NOT NULL,
    format VARCHAR(100) NOT NULL,
    best_of_sets INT NOT NULL,
    set_points INT NOT NULL
);

CREATE TABLE institution_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    contact_email VARCHAR(120) NULL,
    contact_phone VARCHAR(50) NULL,
    website VARCHAR(150) NULL,
    address VARCHAR(200) NULL,
    logo_path VARCHAR(200) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    championship_id INT NOT NULL,
    name VARCHAR(10) NOT NULL,
    FOREIGN KEY (championship_id) REFERENCES championships(id)
);

CREATE TABLE group_players (
    group_id INT NOT NULL,
    player_id INT NOT NULL,
    PRIMARY KEY (group_id, player_id),
    FOREIGN KEY (group_id) REFERENCES groups(id),
    FOREIGN KEY (player_id) REFERENCES players(id)
);

CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    championship_id INT NOT NULL,
    group_id INT NULL,
    player1_id INT NOT NULL,
    player2_id INT NOT NULL,
    player1_sets INT DEFAULT 0,
    player2_sets INT DEFAULT 0,
    sets_json JSON NULL,
    stage ENUM('group','elimination') DEFAULT 'group',
    scheduled_at DATETIME,
    status ENUM('scheduled','played') DEFAULT 'scheduled',
    FOREIGN KEY (championship_id) REFERENCES championships(id),
    FOREIGN KEY (group_id) REFERENCES groups(id),
    FOREIGN KEY (player1_id) REFERENCES players(id),
    FOREIGN KEY (player2_id) REFERENCES players(id)
);

INSERT INTO users (name, email, password, role) VALUES ('Administrador', 'admin@tdm.local', '$2y$10$0u2rtV4oy8JbZLQZ/IQrYO7m8l7Xy0eZAi4D5EONosFVJeFt3PcNu', 'admin');
-- contraseña: admin123
INSERT INTO institution_settings (name, contact_email, contact_phone) VALUES ('Institución organizadora', 'contacto@example.com', '+56 9 1234 5678');
