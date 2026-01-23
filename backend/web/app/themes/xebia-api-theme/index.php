<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xebia Authority Hub - API Backend</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a3e 50%, #2d1b4e 100%);
            min-height: 100vh;
            color: #fff;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .badge {
            background: rgba(107, 33, 168, 0.3);
            border: 1px solid rgba(107, 33, 168, 0.5);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #c084fc;
            margin-bottom: 2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .badge::before {
            content: '';
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #fff 0%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            font-size: 1.25rem;
            color: #a1a1aa;
            margin-bottom: 3rem;
            max-width: 600px;
        }

        .architecture {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .arch-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            backdrop-filter: blur(10px);
        }

        .arch-box.active {
            background: rgba(107, 33, 168, 0.2);
            border-color: rgba(107, 33, 168, 0.5);
        }

        .arch-label {
            font-size: 0.75rem;
            color: #71717a;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .arch-value {
            font-weight: 600;
            color: #fff;
        }

        .arch-arrow {
            color: #6b21a8;
            font-size: 1.5rem;
        }

        .endpoints {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            width: 100%;
            max-width: 900px;
            margin-bottom: 3rem;
        }

        .endpoint {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: left;
            transition: all 0.3s ease;
        }

        .endpoint:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(107, 33, 168, 0.3);
            transform: translateY(-2px);
        }

        .endpoint-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .endpoint-method {
            background: #22c55e;
            color: #000;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .endpoint-method.post {
            background: #3b82f6;
            color: #fff;
        }

        .endpoint-path {
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.875rem;
            color: #c084fc;
        }

        .endpoint-desc {
            font-size: 0.875rem;
            color: #71717a;
        }

        .cta-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6b21a8 0%, #9333ea 100%);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(107, 33, 168, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .tech-stack {
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .tech-label {
            font-size: 0.75rem;
            color: #71717a;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1rem;
        }

        .tech-items {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            justify-content: center;
            color: #a1a1aa;
            font-size: 0.875rem;
        }

        .tech-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @media (max-width: 768px) {
            h1 { font-size: 2rem; }
            .architecture { flex-direction: column; }
            .arch-arrow { transform: rotate(90deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="badge">API Backend Online</div>

        <h1>Xebia Authority Hub</h1>
        <p class="subtitle">
            This is the headless WordPress API backend. Content is delivered via GraphQL to a decoupled Next.js frontend.
        </p>

        <div class="architecture">
            <div class="arch-box">
                <div class="arch-label">Frontend</div>
                <div class="arch-value">Next.js on Vercel</div>
            </div>
            <span class="arch-arrow">&larr;</span>
            <div class="arch-box active">
                <div class="arch-label">API Layer</div>
                <div class="arch-value">WPGraphQL</div>
            </div>
            <span class="arch-arrow">&larr;</span>
            <div class="arch-box">
                <div class="arch-label">CMS</div>
                <div class="arch-value">WordPress + Bedrock</div>
            </div>
        </div>

        <div class="endpoints">
            <div class="endpoint">
                <div class="endpoint-header">
                    <span class="endpoint-method post">POST</span>
                    <span class="endpoint-path">/wp/graphql</span>
                </div>
                <p class="endpoint-desc">GraphQL API endpoint for all content queries and mutations</p>
            </div>
            <div class="endpoint">
                <div class="endpoint-header">
                    <span class="endpoint-method">GET</span>
                    <span class="endpoint-path">/wp/wp-json/</span>
                </div>
                <p class="endpoint-desc">REST API for WordPress core functionality</p>
            </div>
            <div class="endpoint">
                <div class="endpoint-header">
                    <span class="endpoint-method post">POST</span>
                    <span class="endpoint-path">/wp/wp-json/xebia/v1/</span>
                </div>
                <p class="endpoint-desc">Custom REST endpoints for AI summary generation</p>
            </div>
        </div>

        <div class="cta-group">
            <a href="https://hub.kevinchamplin.com" class="btn btn-primary">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                View Frontend
            </a>
            <a href="https://github.com/Kevinchamplin/jd-demo" class="btn btn-secondary">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/>
                </svg>
                View Source
            </a>
        </div>

        <div class="tech-stack">
            <div class="tech-label">Built With</div>
            <div class="tech-items">
                <span class="tech-item">PHP 8.2+</span>
                <span class="tech-item">WordPress + Bedrock</span>
                <span class="tech-item">WPGraphQL</span>
                <span class="tech-item">OpenAI GPT-4o</span>
                <span class="tech-item">Next.js 14</span>
                <span class="tech-item">TypeScript</span>
            </div>
        </div>
    </div>
</body>
</html>
