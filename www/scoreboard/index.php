<?php
require_once '../config/database.php';
$db = new Database();
$scores = $db->getScoreboard();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Scoreboard - Event Judging System</title>
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
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
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
            min-height: 100vh;
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
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="none"><path d="M0,0 L1000,0 L1000,80 Q500,120 0,80 Z" fill="rgba(255,255,255,0.05)"/></svg>') no-repeat center bottom;
            background-size: cover;
        }

        .header-content {
            position: relative;
            z-index: 1;
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

        .live-indicator {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        .refresh-status {
            font-size: 0.75rem;
            opacity: 0.8;
            margin-left: 0.5rem;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Loading overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(248, 250, 252, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--border);
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Main Content */
        .main-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .scoreboard-section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            position: relative;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--background);
        }

        .section-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .section-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        /* Scoreboard Table */
        .table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch; 
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .scoreboard-table {
            width: 100%;
            min-width: 800px; 
            border-collapse: collapse;
            background: white;
        }
        .scoreboard-table th,
        .scoreboard-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap; 
        }

        .scoreboard-table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
            position: sticky;
            top: 0;
            z-index: 10;
        }


        .scoreboard-table tbody tr:hover {
            background-color: #f9fafb;
        }

        .scoreboard-table tr:last-child td {
            border-bottom: none;
        }

        .rank-cell {
            font-weight: 700;
            font-size: 1.125rem;
            width: 60px;
        }

        .participant-cell {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .participant-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--accent-color), #34d399);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            flex-shrink: 0;
        }

        .participant-info {
            flex: 1;
        }

        .participant-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .participant-id {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .scores-cell {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .medal-icon {
            margin-right: 0.5rem;
            font-size: 1.25rem;
        }

        /* Participants Sidebar */
        .participants-section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            height: fit-content;
            position: relative;
        }

        .participants-header {
            background: var(--background);
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .participants-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .participants-count {
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .participants-list {
            max-height: 600px;
            overflow-y: auto;
        }

        .participant-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: var(--transition);
        }

        .participant-item:last-child {
            border-bottom: none;
        }

        .participant-item:hover {
            background: var(--background);
        }

        .participant-stats {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.875rem;
        }

        .stat-label {
            color: var(--text-secondary);
        }

        .stat-value {
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--text-secondary);
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
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
            .table-container {
                margin: 0 -16px; /* Extend to screen edges on mobile */
                border-radius: 0;
            }
            
            .scoreboard-table th,
            .scoreboard-table td {
                padding: 8px 12px;
                font-size: 0.9em;
            }
            
            .participant-avatar {
                width: 28px;
                height: 28px;
                line-height: 28px;
                margin-right: 8px;
            }
        }

        /* Scrollbar styling for webkit browsers */
        .table-container::-webkit-scrollbar {
            height: 8px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Loading Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .scoreboard-section,
        .participants-section {
            animation: fadeInUp 0.6s ease forwards;
        }

        .scoreboard-section {
            animation-delay: 0.1s;
        }

        .participants-section {
            animation-delay: 0.2s;
        }

        /* Update animations */
        .fade-update {
            animation: fadeUpdate 0.5s ease;
        }

        @keyframes fadeUpdate {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <h1>🏆 Live Scoreboard</h1>
                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <div class="live-indicator">
                        <div class="live-dot"></div>
                        Live Updates
                        <span class="refresh-status" id="refreshStatus">Next refresh in 30s</span>
                    </div>
                    <nav class="breadcrumb">
                        <a href="../">← Back to Home</a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="main-content">
            <!-- Scoreboard Section -->
            <div class="scoreboard-section">
                <div class="loading-overlay" id="scoreboardLoader">
                    <div class="loading-spinner"></div>
                </div>
                <div class="section-header">
                    <div class="section-icon">🎯</div>
                    <h2>Current Rankings</h2>
                </div>

                <div id="scoreboardContent">
                    <?php if (empty($scores)): ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">🏆</div>
                            <h4>No Scores Yet</h4>
                            <p>Scores will appear here once judges start submitting evaluations.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="scoreboard-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Participant</th>
                                        <th>Total Points</th>
                                        <th>Average Points</th>
                                        <th>Highest Score</th>
                                        <th>Lowest Score</th>
                                        <th>Scores</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $rank = 1;
                                    foreach ($scores as $score): 
                                        $medal = '';
                                        if ($rank === 1) $medal = '🥇';
                                        elseif ($rank === 2) $medal = '🥈';
                                        elseif ($rank === 3) $medal = '🥉';
                                    ?>
                                    <tr>
                                        <td class="rank-cell">
                                            <span class="medal-icon"><?= $medal ?></span>
                                            #<?= $rank ?>
                                        </td>
                                        <td class="participant-cell">
                                            <div class="participant-avatar">
                                                <?= strtoupper(substr($score['display_name'], 0, 1)) ?>
                                            </div>
                                            <div class="participant-info">
                                                <div class="participant-name">
                                                    <?= htmlspecialchars($score['display_name']) ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="points-cell">
                                            <span class="score-badge">
                                                <?= $score['total_points'] ?>
                                            </span>
                                        </td>
                                        <td class="average-cell">
                                            <span class="score-badge">
                                                <?= $score['average_points'] ?>
                                            </span>
                                        </td>
                                        <td class="highest-cell">
                                            <span class="score-badge">
                                                <?= $score['highest_score'] ?>
                                            </span>
                                        </td>
                                        <td class="lowest-cell">
                                            <span class="score-badge">
                                                <?= $score['lowest_score'] ?>
                                            </span>
                                        </td>
                                        <td class="scores-cell">
                                            <span class="score-badge">
                                                <?= $score['total_scores'] ?> scores
                                            </span>
                                        </td>
                                    </tr>
                                    <?php 
                                    $rank++;
                                    endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Participants Section -->
            <div class="participants-section">
                <div class="loading-overlay" id="participantsLoader">
                    <div class="loading-spinner"></div>
                </div>
                <div class="participants-header">
                    <h3>
                        👥 Participants 
                        <span class="participants-count" id="participantsCount"><?= count($scores) ?></span>
                    </h3>
                </div>
                
                <div id="participantsContent">
                    <?php if (empty($scores)): ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">👤</div>
                            <h4>No Participants Yet</h4>
                            <p>Participants will appear here once they are added to the system.</p>
                        </div>
                    <?php else: ?>
                        <div class="participants-list">
                            <?php foreach ($scores as $score): ?>
                                <div class="participant-item">
                                    <div class="participant-avatar">
                                        <?= strtoupper(substr($score['display_name'], 0, 1)) ?>
                                    </div>
                                    <div class="participant-info">
                                        <div class="participant-name">
                                            <?= htmlspecialchars($score['display_name']) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="participant-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Total Participants:</span>
                                    <span class="stat-value"><?= count($scores) ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Total Scores:</span>
                                    <span class="stat-value"><?= array_sum(array_column($scores, 'total_scores')) ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Highest Score:</span>
                                    <span class="stat-value"><?= !empty($scores) ? max(array_column($scores, 'total_points')) : 0 ?> pts</span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        let refreshTimer;
        let countdownTimer;
        let refreshInterval = 30000; // 30 seconds
        let countdown = 30;

        // Function to fetch updated scoreboard data
        async function fetchScoreboardData() {
            try {
                showLoading(true);
                
                const response = await fetch('../api/get_scores.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                
                if (data.success) {
                    updateScoreboardDisplay(data.scores);
                    updateParticipantsDisplay(data.scores);
                    console.log('Scoreboard updated successfully');
                } else {
                    console.error('Error fetching data:', data.error);
                }
            } catch (error) {
                console.error('Error fetching scoreboard data:', error);
            } finally {
                showLoading(false);
            }
        }

        // Show/hide loading indicators
        function showLoading(show) {
            const scoreboardLoader = document.getElementById('scoreboardLoader');
            const participantsLoader = document.getElementById('participantsLoader');
            
            if (show) {
                scoreboardLoader.classList.add('active');
                participantsLoader.classList.add('active');
            } else {
                scoreboardLoader.classList.remove('active');
                participantsLoader.classList.remove('active');
            }
        }

        // Update scoreboard display
        function updateScoreboardDisplay(scores) {
            const scoreboardContent = document.getElementById('scoreboardContent');
            scoreboardContent.classList.add('fade-update');
            
            setTimeout(() => {
                if (scores.length === 0) {
                    scoreboardContent.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-state-icon">🏆</div>
                            <h4>No Scores Yet</h4>
                            <p>Scores will appear here once judges start submitting evaluations.</p>
                        </div>
                    `;
                } else {
                    let tableHTML = `
                        <table class="scoreboard-table">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Participant</th>
                                    <th>Total Points</th>
                                    <th>Scores</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    
                    scores.forEach((score, index) => {
                        const rank = index + 1;
                        let medal = '';
                        if (rank === 1) medal = '🥇';
                        else if (rank === 2) medal = '🥈';
                        else if (rank === 3) medal = '🥉';
                        
                        const firstLetter = score.display_name.charAt(0).toUpperCase();
                        
                        tableHTML += `
                            <tr>
                                <td class="rank-cell">
                                    <span class="medal-icon">${medal}</span>
                                    #${rank}
                                </td>
                                <td class="participant-cell">
                                    <div class="participant-avatar">
                                        ${firstLetter}
                                    </div>
                                    <div class="participant-info">
                                        <div class="participant-name">
                                            ${escapeHtml(score.display_name)}
                                        </div>
                                    </div>
                                </td>
                                <td class="points-cell">${score.total_points}</td>
                                <td class="scores-cell">${score.total_scores} scores</td>
                            </tr>
                        `;
                    });
                    
                    tableHTML += `
                            </tbody>
                        </table>
                    `;
                    
                    scoreboardContent.innerHTML = tableHTML;
                    
                    // Re-add hover effects
                    addHoverEffects();
                }
                
                scoreboardContent.classList.remove('fade-update');
            }, 250);
        }

        // Update participants display
        function updateParticipantsDisplay(scores) {
            const participantsContent = document.getElementById('participantsContent');
            const participantsCount = document.getElementById('participantsCount');
            
            participantsContent.classList.add('fade-update');
            participantsCount.textContent = scores.length;
            
            setTimeout(() => {
                if (scores.length === 0) {
                    participantsContent.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-state-icon">👤</div>
                            <h4>No Participants Yet</h4>
                            <p>Participants will appear here once they are added to the system.</p>
                        </div>
                    `;
                } else {
                    let participantsHTML = '<div class="participants-list">';
                    
                    scores.forEach(score => {
                        const firstLetter = score.display_name.charAt(0).toUpperCase();
                        
                        participantsHTML += `
                            <div class="participant-item">
                                <div class="participant-avatar">
                                    ${firstLetter}
                                </div>
                                <div class="participant-info">
                                    <div class="participant-name">
                                        ${escapeHtml(score.display_name)}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    const totalScores = scores.reduce((sum, score) => sum + parseInt(score.total_scores), 0);
                    const highestScore = scores.length > 0 ? Math.max(...scores.map(score => parseInt(score.total_points))) : 0;
                    
                    participantsHTML += `
                        <div class="participant-stats">
                            <div class="stat-item">
                                <span class="stat-label">Total Participants:</span>
                                <span class="stat-value">${scores.length}</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Total Scores:</span>
                                <span class="stat-value">${totalScores}</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Highest Score:</span>
                                <span class="stat-value">${highestScore} pts</span>
                            </div>
                        </div>
                    </div>`;
                    
                    participantsContent.innerHTML = participantsHTML;
                }
                
                participantsContent.classList.remove('fade-update');
            }, 250);
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Add hover effects to table rows
        function addHoverEffects() {
            document.querySelectorAll('.scoreboard-table tr').forEach((row, index) => {
                if (index > 0) { // Skip header row
                    row.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateX(4px)';
                    });
                    
                    row.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateX(0)';
                    });
                }
            });
        }

        // Update countdown display
        function updateCountdown() {
            const refreshStatus = document.getElementById('refreshStatus');
            refreshStatus.textContent = `Next refresh in ${countdown}s`;
            
            if (countdown <= 0) {
                countdown = refreshInterval / 1000;
                fetchScoreboardData();
            } else {
                countdown--;
            }
        }

        // Start auto-refresh functionality
        function startAutoRefresh() {
            // Initial countdown setup
            countdown = refreshInterval / 1000;
            
            // Update countdown every second
            countdownTimer = setInterval(updateCountdown, 1000);
            
            // Refresh data at intervals
            refreshTimer = setInterval(() => {
                fetchScoreboardData();
                countdown = refreshInterval / 1000;
            }, refreshInterval);
        }

        // Stop auto-refresh (useful for cleanup)
        function stopAutoRefresh() {
            if (refreshTimer) {
                clearInterval(refreshTimer);
            }
            if (countdownTimer) {
                clearInterval(countdownTimer);
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Add initial hover effects
            addHoverEffects();
            
            // Start auto-refresh
            startAutoRefresh();
            
            // Handle page visibility changes to pause/resume refresh
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopAutoRefresh();
                } else {
                    startAutoRefresh();
                    // Immediately fetch data when page becomes visible
                    fetchScoreboardData();
                }
            });
        });

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            stopAutoRefresh();
        });
    </script>
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Event Judging System.</p>
        </div>
    </footer>
</body>
</html>