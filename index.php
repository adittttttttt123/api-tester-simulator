<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Tester Simulator</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <h2>API Tester Simulator</h2>
            <div>
                <a href="index.php">Dashboard</a>
                <a href="history.php">History</a>
            </div>
        </div>

        <div class="card">
            <h3>Test Configuration</h3>
            <div class="form-group">
                <label for="apiUrl">API URL</label>
                <input type="text" id="apiUrl" class="form-control" placeholder="https://api.example.com/data">
            </div>
            <div class="form-group">
                <label for="method">Method</label>
                <select id="method" class="form-control">
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                </select>
            </div>
            <button id="startTest" class="btn btn-primary">Start Test</button>
            <button id="stopTest" class="btn btn-primary" style="background-color: #f44336;" disabled>Stop Test</button>
        </div>

        <div class="callout">
            <strong>Info:</strong> Simulator is ready to test your API endpoints.
        </div>

        <div class="stats-grid">
            <div class="stat-box">
                <div>Total Requests</div>
                <div class="stat-value" id="totalRequests">0</div>
            </div>
            <div class="stat-box">
                <div>Success</div>
                <div class="stat-value" id="successRequests" style="color: var(--success-color)">0</div>
            </div>
            <div class="stat-box">
                <div>Errors</div>
                <div class="stat-value" id="errorRequests" style="color: var(--error-color)">0</div>
            </div>
            <div class="stat-box">
                <div>Avg Response Time</div>
                <div class="stat-value" id="avgTime">0 ms</div>
            </div>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h3>Performance Chart</h3>
            <canvas id="performanceChart"></canvas>
        </div>
    </div>

    <div class="watermark">Created by <strong>adittttttttt123</strong></div>
    <script src="assets/js/chart-config.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
