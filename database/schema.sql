-- Movie Viewer and Watchlist Recommendation System
-- Database Schema
-- Created: 2025-12-15

-- Drop existing tables if they exist
DROP TABLE IF EXISTS ratings;
DROP TABLE IF EXISTS watchlist;
DROP TABLE IF EXISTS movies;
DROP TABLE IF EXISTS users;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Movies table
CREATE TABLE movies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    genre VARCHAR(100) NOT NULL,
    description TEXT,
    year INT NOT NULL,
    poster VARCHAR(255),
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    rating_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_title (title),
    INDEX idx_genre (genre),
    INDEX idx_year (year),
    INDEX idx_rating (average_rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Watchlist table (many-to-many relationship)
CREATE TABLE watchlist (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_watchlist (user_id, movie_id),
    INDEX idx_user_id (user_id),
    INDEX idx_movie_id (movie_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ratings table
CREATE TABLE ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    rated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_rating (user_id, movie_id),
    INDEX idx_user_id (user_id),
    INDEX idx_movie_id (movie_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Trigger to update movie average rating after insert
DELIMITER //
CREATE TRIGGER update_rating_after_insert
AFTER INSERT ON ratings
FOR EACH ROW
BEGIN
    UPDATE movies 
    SET average_rating = (
        SELECT AVG(rating) 
        FROM ratings 
        WHERE movie_id = NEW.movie_id
    ),
    rating_count = (
        SELECT COUNT(*) 
        FROM ratings 
        WHERE movie_id = NEW.movie_id
    )
    WHERE id = NEW.movie_id;
END//
DELIMITER ;

-- Trigger to update movie average rating after update
DELIMITER //
CREATE TRIGGER update_rating_after_update
AFTER UPDATE ON ratings
FOR EACH ROW
BEGIN
    UPDATE movies 
    SET average_rating = (
        SELECT AVG(rating) 
        FROM ratings 
        WHERE movie_id = NEW.movie_id
    ),
    rating_count = (
        SELECT COUNT(*) 
        FROM ratings 
        WHERE movie_id = NEW.movie_id
    )
    WHERE id = NEW.movie_id;
END//
DELIMITER ;

-- Trigger to update movie average rating after delete
DELIMITER //
CREATE TRIGGER update_rating_after_delete
AFTER DELETE ON ratings
FOR EACH ROW
BEGIN
    UPDATE movies 
    SET average_rating = COALESCE((
        SELECT AVG(rating) 
        FROM ratings 
        WHERE movie_id = OLD.movie_id
    ), 0.00),
    rating_count = (
        SELECT COUNT(*) 
        FROM ratings 
        WHERE movie_id = OLD.movie_id
    )
    WHERE id = OLD.movie_id;
END//
DELIMITER ;
