CREATE DATABASE IF NOT EXISTS clicktoborrow
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE clicktoborrow;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    school VARCHAR(20) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
);

CREATE TABLE IF NOT EXISTS admin (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_admin_email (email)
);

CREATE TABLE IF NOT EXISTS borrow_requests (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    item VARCHAR(100) NOT NULL,
    `date` DATE NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_borrow_requests_user_created (user_email, created_at),
    KEY idx_borrow_requests_status (status)
);
