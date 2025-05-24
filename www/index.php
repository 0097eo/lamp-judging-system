<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Judging System</title>
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #3b82f6;
            --accent-color: #10b981;
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
            padding: 4rem 0;
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
            text-align: center;
        }

        .header h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 700;
            margin-bottom: 1rem;
            letter-spacing: -0.025em;
        }

        .header p {
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            opacity: 0.9;
            font-weight: 400;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Main Content */
        .main-content {
            padding: 4rem 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .section-title p {
            color: var(--text-secondary);
            font-size: 1.125rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Navigation Cards */
        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .nav-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-decoration: none;
            color: inherit;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .nav-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transform: scaleX(0);
            transition: var(--transition);
            transform-origin: left;
        }

        .nav-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
            border-color: var(--secondary-color);
        }

        .nav-card:hover::before {
            transform: scaleX(1);
        }

        .nav-card-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .nav-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
        }

        .nav-card p {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* Technology Stack */
        .tech-section {
            background: var(--surface);
            border-radius: var(--border-radius);
            padding: 3rem 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
        }

        .tech-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .tech-item {
            background: var(--background);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            transition: var(--transition);
        }

        .tech-item:hover {
            border-color: var(--secondary-color);
            box-shadow: var(--shadow-md);
        }

        .tech-item-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            display: block;
        }

        .tech-item h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .tech-item p {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        /* Footer */
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
            .header {
                padding: 3rem 0;
            }

            .main-content {
                padding: 3rem 0;
            }

            .nav-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .nav-card {
                padding: 1.5rem;
            }

            .tech-section {
                padding: 2rem 1rem;
            }

            .tech-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 1rem;
            }

            .tech-item {
                padding: 1rem;
            }

            .container {
                padding: 0 0.75rem;
            }
        }

        @media (max-width: 480px) {
            .nav-grid {
                gap: 1rem;
            }

            .nav-card {
                padding: 1.25rem;
            }

            .tech-grid {
                grid-template-columns: 1fr 1fr;
            }

            .section-title {
                margin-bottom: 2rem;
            }

            .main-content {
                padding: 2rem 0;
            }
        }

        /* Focus states for accessibility */
        .nav-card:focus {
            outline: 2px solid var(--secondary-color);
            outline-offset: 2px;
        }

        /* Loading animation */
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

        .nav-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .nav-card:nth-child(1) { animation-delay: 0.1s; }
        .nav-card:nth-child(2) { animation-delay: 0.2s; }
        .nav-card:nth-child(3) { animation-delay: 0.3s; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <h1>Event Judging System</h1>
                <p>Competition management platform.</p>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <section class="section-title">
                <h2>System Access</h2>
                <p>Choose your role to access the appropriate interface</p>
            </section>

            <div class="nav-grid">
                <a href="admin/" class="nav-card">
                    <div class="nav-card-icon">👨‍💼</div>
                    <h3>Admin Panel</h3>
                    <p>Comprehensive administration interface for managing judges, participants, and system configuration. Full control over competition settings and user permissions.</p>
                </a>

                <a href="judge/" class="nav-card">
                    <div class="nav-card-icon">⚖️</div>
                    <h3>Judges Portal</h3>
                    <p>Streamlined scoring interface for competition judges. View participant details, submit scores, and track evaluation progress in real-time.</p>
                </a>

                <a href="scoreboard/" class="nav-card">
                    <div class="nav-card-icon">📊</div>
                    <h3>Public Scoreboard</h3>
                    <p>Live competition results and rankings. Real-time updates with comprehensive statistics and transparent scoring breakdown for all participants.</p>
                </a>
            </div>

            <section class="tech-section">
                <div class="section-title">
                    <h2>Technology Stack</h2>
                    <p>Built with industry-standard technologies for reliability and performance</p>
                </div>

                <div class="tech-grid">
                    <div class="tech-item">
                        <span class="tech-item-icon">🐧</span>
                        <h4>Linux</h4>
                        <p>Docker Container</p>
                    </div>
                    <div class="tech-item">
                        <span class="tech-item-icon">🌐</span>
                        <h4>Apache</h4>
                        <p>Web Server</p>
                    </div>
                    <div class="tech-item">
                        <span class="tech-item-icon">🗄️</span>
                        <h4>MySQL 8.0</h4>
                        <p>Database</p>
                    </div>
                    <div class="tech-item">
                        <span class="tech-item-icon">🐘</span>
                        <h4>PHP 8.1</h4>
                        <p>Backend</p>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Event Judging System.</p>
        </div>
    </footer>
</body>
</html>