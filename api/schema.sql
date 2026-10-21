-- Portfolio CMS schema (MySQL 8 / MariaDB compatible) — Hostinger
CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,           -- password_hash(), argon2id
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS auth_tokens (
  token CHAR(64) PRIMARY KEY,                    -- random_bytes(32) hex
  admin_id INT UNSIGNED NOT NULL,
  expires_at TIMESTAMP NOT NULL,
  FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS projects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(190) NOT NULL UNIQUE,
  title VARCHAR(190) NOT NULL,
  category VARCHAR(60) DEFAULT 'Mobile',
  year VARCHAR(20) DEFAULT '',
  client VARCHAR(120) DEFAULT '',
  role VARCHAR(120) DEFAULT '',
  description TEXT,
  stack JSON,                                     -- ["Flutter","Riverpod"]
  store_urls JSON,                                -- {"ios":"...","android":"..."}
  is_featured TINYINT(1) DEFAULT 0,
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS experiences (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company VARCHAR(150) NOT NULL,
  position VARCHAR(150) NOT NULL,
  period VARCHAR(40) NOT NULL,
  location VARCHAR(120) DEFAULT '',
  highlight TEXT,                                 -- one distilled line for the timeline
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS skills (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  grp VARCHAR(40) NOT NULL,                       -- AI | Full Stack | Mobile
  name VARCHAR(80) NOT NULL,
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS testimonials (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  quote TEXT NOT NULL,
  author VARCHAR(120) NOT NULL,
  note VARCHAR(120) DEFAULT '',
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS profile (
  k VARCHAR(60) PRIMARY KEY,                      -- name, headline, email, stats_json...
  v TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
