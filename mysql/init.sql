USE judging_system;

DROP VIEW IF EXISTS scoreboard;
DROP TABLE IF EXISTS scores;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS judges;

-- Create judges table
CREATE TABLE judges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
);

-- Create regular users/participants table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
);

-- Create scores table 
CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judge_id INT NOT NULL,
    user_id INT NOT NULL,
    points INT NOT NULL CHECK (points >= 0 AND points <= 100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (judge_id) REFERENCES judges(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_judge_id (judge_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
);

-- Insert sample judges
INSERT INTO judges (username, display_name) VALUES
('judge1', 'Judge Sarah Wilson'),
('judge2', 'Judge Michael Chen'),
('judge3', 'Judge Emma Rodriguez'),
('judge4', 'Judge David Kumar'),
('judge5', 'Judge Lisa Martinez');

-- Insert sample users/participants
INSERT INTO users (username, display_name) VALUES
('participant1', 'Alex Thompson'),
('participant2', 'Jordan Smith'),
('participant3', 'Casey Johnson'),
('participant4', 'Riley Davis'),
('participant5', 'Taylor Brown'),
('participant6', 'Morgan Wilson'),
('participant7', 'Avery Garcia'),
('participant8', 'Quinn Anderson');

-- Insert sample 
INSERT INTO scores (judge_id, user_id, points) VALUES
-- Judge 1 scores
(1, 1, 85),
(1, 2, 92),
(1, 3, 78),
(1, 1, 75),
(1, 1, 90),
(1, 4, 88),

-- Judge 2 scores
(2, 1, 88),
(2, 2, 90),
(2, 4, 95),
(2, 2, 85),
(2, 5, 82),

-- Judge 3 scores
(3, 1, 82),
(3, 3, 85),
(3, 5, 91),
(3, 1, 88),
(3, 6, 79),

-- Judge 4 scores
(4, 2, 94),
(4, 3, 81),
(4, 4, 87),
(4, 7, 93),

-- Judge 5 scores
(5, 1, 86),
(5, 5, 89),
(5, 6, 84),
(5, 8, 92);

-- Create comprehensive scoreboard view
CREATE VIEW scoreboard AS
SELECT 
    u.id,
    u.username,
    u.display_name,
    COALESCE(SUM(s.points), 0) as total_points,
    COUNT(s.id) as total_scores,
    COALESCE(ROUND(AVG(s.points), 2), 0) as average_points,
    COALESCE(MAX(s.points), 0) as highest_score,
    COALESCE(MIN(s.points), 0) as lowest_score,
    COUNT(DISTINCT s.judge_id) as judges_count
FROM users u
LEFT JOIN scores s ON u.id = s.user_id
GROUP BY u.id, u.username, u.display_name
ORDER BY total_points DESC, average_points DESC;


