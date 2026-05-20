<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test History - API Tester</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        th {
            background-color: #1e1e24;
            color: #03dac6;
        }
    </style>
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
            <h3>Aggregated Test History</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>URL</th>
                            <th>Method</th>
                            <th>Total Req</th>
                            <th>Avg Time</th>
                            <th>Success %</th>
                            <th>2xx</th>
                            <th>4xx</th>
                            <th>5xx</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        <tr><td colspan="10" style="text-align:center;">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const tableBody = document.getElementById('historyTableBody');
            
            try {
                const response = await fetch('api/get-history.php');
                const result = await response.json();
                
                if (result.status === 'success') {
                    tableBody.innerHTML = '';
                    if (result.data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="10" style="text-align:center;">No history found</td></tr>';
                    } else {
                        result.data.forEach(row => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${row.id}</td>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${row.url_target}">${row.url_target}</td>
                                <td>${row.http_method}</td>
                                <td>${row.total_requests}</td>
                                <td>${parseFloat(row.avg_response_time).toFixed(2)} ms</td>
                                <td><span style="color: ${row.success_rate > 90 ? '#03dac6' : '#cf6679'}">${parseFloat(row.success_rate).toFixed(2)}%</span></td>
                                <td style="color: #03dac6">${row.status_2xx}</td>
                                <td style="color: #ffb74d">${row.status_4xx}</td>
                                <td style="color: #cf6679">${row.status_5xx}</td>
                                <td>${new Date(row.created_at).toLocaleString()}</td>
                            `;
                            tableBody.appendChild(tr);
                        });
                    }
                } else {
                    tableBody.innerHTML = `<tr><td colspan="10" style="text-align:center; color: var(--error-color)">Error: ${result.message}</td></tr>`;
                }
            } catch (err) {
                tableBody.innerHTML = `<tr><td colspan="10" style="text-align:center; color: var(--error-color)">Failed to load history</td></tr>`;
            }
        });
    </script>
    <div class="watermark">Created by <strong>adittttttttt123</strong></div>
</body>
</html>
