<?php
require_once '../config/database.php';
$db = new Database();

// Handle add judge
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_judge'])) {
    $username = trim($_POST['judge_username']);
    $display_name = trim($_POST['judge_display_name']);
    if ($username && $display_name) {
        try {
            $db->addJudge($username, $display_name);
            $success = "Judge added successfully.";
        } catch (Exception $e) {
            $error = "Error adding judge: " . $e->getMessage();
        }
    }
}

// Handle fetch judge scores
$selected_judge_id = null;
$judge_scores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['get_scores'])) {
    $selected_judge_id = intval($_POST['judge_id']);
    $judge_scores = $db->getJudgeScores($selected_judge_id);
}

$judges = $db->getJudges();
$users = $db->getUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Event Judging System</title>
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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            font-size: 16px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header h1 {
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            backdrop-filter: blur(10px);
        }

        .breadcrumb a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: var(--success-color);
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background-color: #fef2f2;
            color: var(--error-color);
            border: 1px solid #fecaca;
        }

        /* Grid Layout */
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .admin-section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .admin-section:hover {
            box-shadow: var(--shadow-md);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--background);
        }

        .section-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .section-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            background: var(--surface);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgb(59 130 246 / 0.1);
        }

        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            font-size: 1rem;
            background: var(--surface);
            cursor: pointer;
            transition: var(--transition);
        }

        .form-select:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgb(59 130 246 / 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            text-align: center;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: var(--background);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--surface);
            border-color: var(--secondary-color);
        }

        /* Lists */
        .data-list {
            background: var(--surface);
            border-radius: var(--border-radius);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .list-header {
            background: var(--background);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            color: var(--text-primary);
        }

        .list-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: var(--transition);
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .list-item:hover {
            background: var(--background);
        }

        .list-item-content {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .list-item-title {
            font-weight: 500;
            color: var(--text-primary);
        }

        .list-item-subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .score-badge {
            background: var(--accent-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Full Width Sections */
        .full-width-section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            box-shadow: var(--shadow-sm);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            display: block;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.5rem;
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .admin-grid {
                grid-template-columns: 1fr;
            }

            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .container {
                padding: 0 0.75rem;
            }

            .admin-section {
                padding: 1.5rem;
            }

            .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .header {
                padding: 1.5rem 0;
            }

            .admin-section {
                padding: 1rem;
            }
        }

        /* Loading states */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .admin-section {
            animation: fadeIn 0.5s ease forwards;
        }

        .admin-section:nth-child(1) { animation-delay: 0.1s; }
        .admin-section:nth-child(2) { animation-delay: 0.2s; }
        .admin-section:nth-child(3) { animation-delay: 0.3s; }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            padding: 12px 20px;
            border-radius: 8px;
            color: #fff;
            animation:  slideIn 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .toast-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .toast-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(-20px); }
            10% { opacity: 1; transform: translateY(0); }
            90% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <h1>👨‍💼 Admin Panel</h1>
                <nav class="breadcrumb">
                    <a href="../">← Back to Home</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="container">

        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number"><?= count($judges) ?></span>
                <div class="stat-label">Total Judges</div>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= count($users) ?></span>
                <div class="stat-label">Total Participants</div>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= count($judge_scores) ?></span>
                <div class="stat-label">Scores Viewed</div>
            </div>
        </div>

        <!-- Admin Actions -->
        <div class="admin-grid">
            <!-- Add Judge Section -->
            <div class="admin-section">
                <div class="section-header">
                    <div class="section-icon">⚖️</div>
                    <h2>Add Judge</h2>
                </div>
                <form method="post">
                    <input type="hidden" name="add_judge" value="1">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="judge_username" class="form-input" required 
                               placeholder="Enter judge username">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Display Name</label>
                        <input type="text" name="judge_display_name" class="form-input" required 
                               placeholder="Enter display name">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <span>➕</span> Add Judge
                    </button>
                </form>
            </div>

            <!-- View Judge Scores Section -->
            <div class="admin-section">
                <div class="section-header">
                    <div class="section-icon">📊</div>
                    <h2>Judge Scores</h2>
                </div>
                <form method="post">
                    <input type="hidden" name="get_scores" value="1">
                    <div class="form-group">
                        <label class="form-label">Select Judge</label>
                        <select name="judge_id" class="form-select" required>
                            <option value="">-- Select Judge --</option>
                            <?php foreach ($judges as $judge): ?>
                                <option value="<?= $judge['id'] ?>" <?= $selected_judge_id == $judge['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($judge['display_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <span>🔍</span> View Scores
                    </button>
                </form>

                <?php if (!empty($judge_scores)): ?>
                    <div class="data-list" style="margin-top: 1.5rem;">
                        <div class="list-header">
                            Scores for <?= htmlspecialchars($judges[array_search($selected_judge_id, array_column($judges, 'id'))]['display_name']) ?>
                        </div>
                        <?php foreach ($judge_scores as $score): ?>
                            <div class="list-item">
                                <div class="list-item-content">
                                    <div class="list-item-title"><?= htmlspecialchars($score['user_name']) ?></div>
                                </div>
                                <div class="score-badge"><?= $score['points'] ?> pts</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Data Lists -->
        <div class="admin-grid">
            <!-- All Judges -->
            <div class="full-width-section">
                <div class="list-header">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span>⚖️</span>
                        <strong>All Judges (<?= count($judges) ?>)</strong>
                    </div>
                </div>
                <?php if (empty($judges)): ?>
                    <div class="list-item">
                        <div class="list-item-content">
                            <div class="list-item-subtitle">No judges added yet</div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($judges as $judge): ?>
                        <div class="list-item">
                            <div class="list-item-content">
                                <div class="list-item-title"><?= htmlspecialchars($judge['display_name']) ?></div>
                                <div class="list-item-subtitle">@<?= htmlspecialchars($judge['username']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- All Participants -->
            <div class="full-width-section">
                <div class="list-header">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span>👤</span>
                        <strong>All Participants (<?= count($users) ?>)</strong>
                    </div>
                </div>
                <?php if (empty($users)): ?>
                    <div class="list-item">
                        <div class="list-item-content">
                            <div class="list-item-subtitle">No participants added yet</div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <div class="list-item">
                            <div class="list-item-content">
                                <div class="list-item-title"><?= htmlspecialchars($user['display_name']) ?></div>
                                <div class="list-item-subtitle">@<?= htmlspecialchars($user['username']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Event Judging System.</p>
        </div>
    </footer>
    <script>
        function showMessage(message, type) {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        <?php if (!empty($success)): ?>
            showMessage(<?= json_encode($success) ?>, 'success');
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            showMessage(<?= json_encode($error) ?>, 'error');
        <?php endif; ?>
    </script>
</body>
</html>