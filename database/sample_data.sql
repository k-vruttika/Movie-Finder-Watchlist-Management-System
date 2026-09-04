-- Sample Data for Movie Viewer System
-- Insert test users and sample movies

-- Insert users (passwords are hashed versions of 'password123' and 'admin123')
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@movieviewer.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Insert sample movies
INSERT INTO movies (title, genre, description, year, poster, average_rating, rating_count) VALUES
-- Action Movies
('The Dark Knight', 'Action', 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.', 2008, 'dark_knight.jpg', 4.8, 0),
('Mad Max: Fury Road', 'Action', 'In a post-apocalyptic wasteland, a woman rebels against a tyrannical ruler in search for her homeland with the aid of a group of female prisoners, a psychotic worshiper, and a drifter named Max.', 2015, 'mad_max.jpg', 4.6, 0),
('John Wick', 'Action', 'An ex-hit-man comes out of retirement to track down the gangsters that killed his dog and took everything from him.', 2014, 'john_wick.jpg', 4.5, 0),

-- Sci-Fi Movies
('Inception', 'Sci-Fi', 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.', 2010, 'inception.jpg', 4.9, 0),
('Interstellar', 'Sci-Fi', 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity\'s survival.', 2014, 'interstellar.jpg', 4.7, 0),
('The Matrix', 'Sci-Fi', 'A computer hacker learns from mysterious rebels about the true nature of his reality and his role in the war against its controllers.', 1999, 'matrix.jpg', 4.8, 0),
('Blade Runner 2049', 'Sci-Fi', 'Young Blade Runner K\'s discovery of a long-buried secret leads him to track down former Blade Runner Rick Deckard, who\'s been missing for thirty years.', 2017, 'blade_runner.jpg', 4.4, 0),

-- Drama Movies
('The Shawshank Redemption', 'Drama', 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.', 1994, 'shawshank.jpg', 5.0, 0),
('Forrest Gump', 'Drama', 'The presidencies of Kennedy and Johnson, the Vietnam War, and other historical events unfold from the perspective of an Alabama man with an IQ of 75.', 1994, 'forrest_gump.jpg', 4.6, 0),
('The Godfather', 'Drama', 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.', 1972, 'godfather.jpg', 4.9, 0),

-- Comedy Movies
('The Grand Budapest Hotel', 'Comedy', 'A writer encounters the owner of an aging high-class hotel, who tells him of his early years serving as a lobby boy in the hotel\'s glorious years under an exceptional concierge.', 2014, 'budapest.jpg', 4.3, 0),
('Superbad', 'Comedy', 'Two co-dependent high school seniors are forced to deal with separation anxiety after their plan to stage a booze-soaked party goes awry.', 2007, 'superbad.jpg', 4.1, 0),
('Knives Out', 'Comedy', 'A detective investigates the death of a patriarch of an eccentric, combative family.', 2019, 'knives_out.jpg', 4.4, 0),

-- Thriller Movies
('Se7en', 'Thriller', 'Two detectives, a rookie and a veteran, hunt a serial killer who uses the seven deadly sins as his motives.', 1995, 'seven.jpg', 4.7, 0),
('Gone Girl', 'Thriller', 'With his wife\'s disappearance having become the focus of an intense media circus, a man sees the spotlight turned on him when it\'s suspected that he may not be innocent.', 2014, 'gone_girl.jpg', 4.5, 0),
('Prisoners', 'Thriller', 'When Keller Dover\'s daughter and her friend go missing, he takes matters into his own hands as the police pursue multiple leads and the pressure mounts.', 2013, 'prisoners.jpg', 4.6, 0),

-- Romance Movies
('La La Land', 'Romance', 'While navigating their careers in Los Angeles, a pianist and an actress fall in love while attempting to reconcile their aspirations for the future.', 2016, 'lalaland.jpg', 4.2, 0),
('The Notebook', 'Romance', 'A poor yet passionate young man falls in love with a rich young woman, giving her a sense of freedom, but they are soon separated because of their social differences.', 2004, 'notebook.jpg', 4.3, 0),
('Eternal Sunshine of the Spotless Mind', 'Romance', 'When their relationship turns sour, a couple undergoes a medical procedure to have each other erased from their memories.', 2004, 'eternal_sunshine.jpg', 4.5, 0),

-- Horror Movies
('Get Out', 'Horror', 'A young African-American visits his white girlfriend\'s parents for the weekend, where his simmering uneasiness about their reception of him eventually reaches a boiling point.', 2017, 'get_out.jpg', 4.4, 0),
('A Quiet Place', 'Horror', 'In a post-apocalyptic world, a family is forced to live in silence while hiding from monsters with ultra-sensitive hearing.', 2018, 'quiet_place.jpg', 4.3, 0),
('Hereditary', 'Horror', 'A grieving family is haunted by tragic and disturbing occurrences after the death of their secretive grandmother.', 2018, 'hereditary.jpg', 4.2, 0);

-- Insert sample watchlist entries
INSERT INTO watchlist (user_id, movie_id) VALUES
(2, 1),  -- john_doe added The Dark Knight
(2, 4),  -- john_doe added Inception
(2, 8),  -- john_doe added The Shawshank Redemption
(3, 4),  -- jane_smith added Inception
(3, 5),  -- jane_smith added Interstellar
(3, 17); -- jane_smith added La La Land

-- Insert sample ratings
INSERT INTO ratings (user_id, movie_id, rating) VALUES
(2, 1, 5),  -- john_doe rated The Dark Knight 5 stars
(2, 4, 5),  -- john_doe rated Inception 5 stars
(2, 8, 5),  -- john_doe rated The Shawshank Redemption 5 stars
(3, 4, 5),  -- jane_smith rated Inception 5 stars
(3, 5, 5),  -- jane_smith rated Interstellar 5 stars
(3, 17, 4); -- jane_smith rated La La Land 4 stars
