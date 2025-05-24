<?php
require_once '../config/database.php';

// Assume logged in as judge ID 1 for demo
$current_judge_id = 1;

$db = new Database();
$judges = $db->getJudges();
$users = $db->getUsers();

// Get current judge info
$current_judge = null;
foreach ($judges as $judge) {
    if ($judge['id'] == $current_judge_id) {
        $current_judge = $judge;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judge Portal - Scoring System</title>
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #3b82f6;
            --accent-color: #10b981;
            --success-color: #059669;
            --error-color: #dc2626;
            --warning-color: #d97706;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --background: #f8fafc;
            --surface: #ffffff;
            --border: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --border-radius: 8px;
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--background);
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            min-height: 20px;
        }

        .header h1 {
            font-size: clamp(1.8rem, 4vw, 3rem);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.5rem 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 0.5rem 0;
        }

        .judge-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            opacity: 0.9;
            flex-wrap: wrap;
        }

        .breadcrumb a {
            color: white;
            text-decoration: none;
            transition: var(--transition);
            padding: 0.75rem 1.25rem;
            border-radius: var(--border-radius);
            backdrop-filter: blur(10px);
        }

        .breadcrumb a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2rem;
        }

        .main-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            background: #f8f9fa;
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-header .icon {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .card-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            
        }

        .card-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 8px;
            font-size: 1rem;
            background: white;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-select:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        .score-section {
            text-align: center;
        }

        .score-input {
            width: 100%;
            height: 80px;
            border: none;
            background: #f8f9fa;
            border-radius: 12px;
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            margin: 1rem auto;
            display: block;
            color: #2c3e50;
        }

        .score-input:focus {
            outline: none;
            background: #e9ecef;
        }

        .score-range {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }

        .score-categories {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            margin: 1.5rem 0;
        }

        .score-category {
            padding: 0.5rem;
            border-radius: 8px;
            text-align: center;
            font-size: 0.75rem;
        }

        .score-category.poor {
            background: #fee;
            color: #d63384;
        }

        .score-category.fair {
            background: #fff3cd;
            color: #f57c00;
        }

        .score-category.good {
            background: #d1f2eb;
            color: #198754;
        }

        .score-category.excellent {
            background: #cce5ff;
            color: #0d6efd;
        }

        .quick-scores {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .quick-score-btn {
            padding: 0.75rem;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .quick-score-btn:hover {
            background: #f8f9fa;
            border-color: #4a90e2;
        }

        .quick-score-btn .score {
            font-size: 1.25rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .quick-score-btn .label {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 1.5rem;
        }

        .submit-btn:hover {
            background: #3b82f6;
        }

        .submit-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }

        .sidebar {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            height: fit-content;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-header .icon {
            color: var(--primary-color);
            font-size: 1.25rem;
        }

        .sidebar-header h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .participant-count {
            background: var(--primary-color);
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            margin-left: auto;
        }

        .participants-list {
            max-height: 500px;
            overflow-y: auto;
        }

        .participant-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f3f4;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .participant-item:hover {
            background: #f8f9fa;
        }

        .participant-item.selected {
            background: #e3f2fd;
            border-right: 3px solid #4a90e2;
        }

        .participant-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #28a745;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .participant-name {
            font-weight: 500;
            color: #2c3e50;
        }

        .messages {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .message {
            padding: 1rem 1.5rem;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            animation: slideIn 0.3s ease;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .footer {
            background: var(--text-primary);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 4rem;
        }

        .footer p {
            opacity: 0.8;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

       @media (max-width: 768px) {
            .header {
                padding: 2rem 1rem;
            }
            
            .header-content {
                flex-direction: column;
                gap: 1.5rem;
                text-align: center;
                min-height: 100px;
            }
            
            .header h1 {
                font-size: clamp(1.5rem, 6vw, 2.5rem);
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>⚖️ Judge Portal</h1>
            <div class="header-right">
                <div class="judge-info">
                    Judge ID: #<?php echo $current_judge_id; ?> (Demo)
                </div>
                <nav class="breadcrumb">
                    <a href="../">← Back to Home</a>
                </nav>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="main-content">
            <div class="card-header">
                <div class="icon">🎯</div>
                <h2>Submit Score</h2>
            </div>
            <div class="card-body">
                <form id="scoringForm">
                    <div class="form-group">
                        <label class="form-label">SELECT PARTICIPANT</label>
                        <select class="form-select" id="participantSelect" required>
                            <option value="">Choose a participant...</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['display_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">SCORE (0-100 POINTS)</label>
                        <div class="score-section">
                            <input type="number" id="scoreInput" class="score-input" min="0" max="100" value="50" required>
                            <div class="score-range">
                                <span>Minimum: 0</span>
                                <span>Maximum: 100</span>
                            </div>
                            
                            <div class="score-categories">
                                <div class="score-category poor">
                                    0-25<br>Poor
                                </div>
                                <div class="score-category fair">
                                    26-50<br>Fair
                                </div>
                                <div class="score-category good">
                                    51-75<br>Good
                                </div>
                                <div class="score-category excellent">
                                    76-100<br>Excellent
                                </div>
                            </div>

                            <div class="quick-scores">
                                <button type="button" class="quick-score-btn" onclick="setScore(25)">
                                    <div class="score">25</div>
                                    <div class="label">Poor</div>
                                </button>
                                <button type="button" class="quick-score-btn" onclick="setScore(50)">
                                    <div class="score">50</div>
                                    <div class="label">Fair</div>
                                </button>
                                <button type="button" class="quick-score-btn" onclick="setScore(75)">
                                    <div class="score">75</div>
                                    <div class="label">Good</div>
                                </button>
                                <button type="button" class="quick-score-btn" onclick="setScore(100)">
                                    <div class="score">100</div>
                                    <div class="label">Perfect</div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">Submit Score</button>
                </form>
            </div>
        </div>

        <div class="sidebar">
            <div class="sidebar-header">
                <span class="icon">👥</span>
                <h3>Participants</h3>
                <span class="participant-count"><?php echo count($users); ?></span>
            </div>
            <div class="participants-list">
                <?php foreach ($users as $user): ?>
                    <div class="participant-item" onclick="selectParticipant(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['display_name']); ?>')">
                        <div class="participant-avatar">
                            <?php echo strtoupper(substr($user['display_name'], 0, 1)); ?>
                        </div>
                        <div class="participant-name">
                            <?php echo htmlspecialchars($user['display_name']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div id="messages" class="messages"></div>

    <script>
        const currentJudgeId = <?php echo $current_judge_id; ?>;
        const currentJudge = <?php echo json_encode($current_judge); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('scoringForm').addEventListener('submit', handleScoreSubmission);
        });

        function selectParticipant(participantId, participantName) {
            document.getElementById('participantSelect').value = participantId;
            
            document.querySelectorAll('.participant-item').forEach(item => {
                item.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
        }

        function setScore(score) {
            document.getElementById('scoreInput').value = score;
        }

        function handleScoreSubmission(event) {
            event.preventDefault();
            
            const participantId = parseInt(document.getElementById('participantSelect').value);
            const points = parseInt(document.getElementById('scoreInput').value);
            
            if (!participantId) {
                showMessage('Please select a participant first.', 'error');
                return;
            }
            
            if (points < 0 || points > 100) {
                showMessage('Score must be between 0 and 100.', 'error');
                return;
            }
            
            submitScore(currentJudgeId, participantId, points);
        }

        function submitScore(judgeId, participantId, points) {
            const formData = new FormData();
            formData.append('judge_id', judgeId);
            formData.append('user_id', participantId);
            formData.append('points', points);

            fetch('../api/add_score.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showMessage('Score submitted successfully!', 'success');
                    document.getElementById('participantSelect').value = '';
                    document.getElementById('scoreInput').value = '50';
                    document.querySelectorAll('.participant-item').forEach(item => {
                        item.classList.remove('selected');
                    });
                } else {
                    showMessage('Error submitting score: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                showMessage('Network error: ' + error.message, 'error');
            });
        }

        function showMessage(message, type) {
            const messagesDiv = document.getElementById('messages');
            const messageEl = document.createElement('div');
            messageEl.textContent = message;
            messageEl.className = `message ${type}`;
            
            messagesDiv.appendChild(messageEl);
            
            // Remove message after 3 seconds
            setTimeout(() => {
                if (messageEl.parentNode) {
                    messageEl.parentNode.removeChild(messageEl);
                }
            }, 3000);
        }
    </script>
    <footer class="footer">
        <p>&copy; 2025 Event Judging System.</p>
    </footer>
</body>
</html>