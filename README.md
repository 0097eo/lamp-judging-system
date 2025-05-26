# Judging System (LAMP Stack)
- A simple judging platform built on the LAMP stack (Linux, Apache, MySQL, PHP) using Docker.

## Getting Started

### 1. Clone the repository
```
git clone https://github.com/0097eo/lamp-judging-system.git

```
### 2. Run with docker
```
docker-compose up --build
```
- This will:
   - Start PHP + Apache
   - Initialize MySQL with the schema in mysql/init.sql
   - Serve the web app at http://localhost:8000
  
## Database Schema
Judges
```
CREATE TABLE judges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
);

```

users
```
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
);

```

scores
```
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

```

## Assumptions
- No login is required for Judges or Admins in this version.
- Users are assumed to be pre-registered (hardcoded or added manually).
- Judges can only assign scores once per user (update on conflict).
- Frontend is minimal and uses simple HTML/CSS and JavaScript.

## Design Choices
- Dockerized LAMP Stack: Ensures portability and reproducibility of the environment.
- PDO for Database Access: Enables prepared statements, better security, and flexibility.
- Directory Separation:
   - admin/: For judge management
   - judge/: For scoring users
   - scoreboard/: Public view of scores

- MySQL Constraints:
   - UNIQUE ensures no duplicate judge-user scoring.
   - CHECK on score value ensures valid inputs.
 
## Features
- Admin Panel
   - Add new judges via a simple form.

- Judge Portal
   - View list of users.
   - Assign scores to individual users.

- Public Scoreboard
   - View users sorted by total points (DESC).
   - Auto-refreshes every 30 seconds (via JS setInterval).

## Potential Enhancements
- Secure login system for Judges and Admins.
- Better UI using Bootstrap or Vue/React.
- Judge score history.
- Real-time updates via WebSockets.
