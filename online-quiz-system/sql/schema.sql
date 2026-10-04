-- Online Quiz System Database Schema
-- Import this file into MySQL via phpMyAdmin or CLI:
--   mysql -u root -p < schema.sql

CREATE DATABASE IF NOT EXISTS quiz_system
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE quiz_system;

-- Users table
CREATE TABLE users (
  user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_role (role)
) ENGINE=InnoDB;

-- Quiz categories
CREATE TABLE categories (
  category_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Quizzes
CREATE TABLE quizzes (
  quiz_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  description TEXT,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(category_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX idx_category (category_id),
  INDEX idx_status (status)
) ENGINE=InnoDB;

-- Questions (four options, one correct)
CREATE TABLE questions (
  question_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  quiz_id INT UNSIGNED NOT NULL,
  question_text TEXT NOT NULL,
  option_a VARCHAR(500) NOT NULL,
  option_b VARCHAR(500) NOT NULL,
  option_c VARCHAR(500) NOT NULL,
  option_d VARCHAR(500) NOT NULL,
  correct_option ENUM('A', 'B', 'C', 'D') NOT NULL,
  FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_quiz (quiz_id)
) ENGINE=InnoDB;

-- Quiz attempt results
CREATE TABLE results (
  result_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  quiz_id INT UNSIGNED NOT NULL,
  score INT UNSIGNED NOT NULL DEFAULT 0,
  total_questions INT UNSIGNED NOT NULL,
  attempt_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_user (user_id),
  INDEX idx_quiz_result (quiz_id),
  INDEX idx_attempt_date (attempt_date)
) ENGINE=InnoDB;
