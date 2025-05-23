-- init db
USE judging_system;

-- create judges table
CREATE TABLE judges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- create regular users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- create scores table
CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judge_id INT NOT NULL,
    user_id INT NOT NULL,
    points INT NOT NULL CHECK (points >= 0 AND points <= 100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (judge_id) REFERENCES judges(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_judge_user (judge_id, user_id)
);

-- insert dummy data
INSERT INTO judges (username, display_name) VALUES
('judge1', 'Judge Sarah Wilson'),
('judge2', 'Judge Michael Chen'),
('judge3', 'Judge Emma Rodriguez');

INSERT INTO users (username, display_name) VALUES
('participant1', 'Alex Thompson'),
('participant2', 'Jordan Smith'),
('participant3', 'Casey Johnson'),
('participant4', 'Riley Davis'),
('participant5', 'Taylor Brown');

INSERT INTO scores (judge_id, user_id, points) VALUES
(1, 1, 85),
(1, 2, 92),
(1, 3, 78),
(2, 1, 88),
(2, 2, 90),
(2, 4, 95),
(3, 1, 82),
(3, 3, 85),
(3, 5, 91);

-- create a view for scoreboard
CREATE VIEW scoreboard AS
SELECT 
    u.id,
    u.username,
    u.display_name,
    COALESCE(SUM(s.points), 0) as total_points,
    COUNT(s.id) as total_scores
FROM users u
LEFT JOIN scores s ON u.id = s.user_id
GROUP BY u.id, u.username, u.display_name
ORDER BY total_points DESC;