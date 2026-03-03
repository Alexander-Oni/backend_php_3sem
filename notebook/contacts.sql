CREATE DATABASE IF NOT EXISTS notebook_ver2;
CREATE TABLE contacts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    last_name   VARCHAR(100) NOT NULL,
    first_name  VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) DEFAULT '',
    gender      ENUM('M','F') DEFAULT NULL,
    birthdate   DATE DEFAULT NULL,
    phone       VARCHAR(50) DEFAULT '',
    address     VARCHAR(255) DEFAULT '',
    email       VARCHAR(150) DEFAULT '',
    comment     TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
