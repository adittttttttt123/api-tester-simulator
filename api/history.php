<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test History - API Tester</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            border-bottom: 1px solid var(--border-color);
        }
        th {
            background-color: var(--card-bg);
            color: var(--secondary-color);
        }
        .btn-small {
            padding: 5px 10px;
            font-size: 0.8em;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-small:hover {
            background-color: #7c4dff;
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
        }
        .modal-content {
            background-color: var(--card-bg);
            margin: 5% auto;
            padding: 20px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.5);
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <h2>API Tester Simulator</h2>
            <div>
                <a href="/index.php">Dashboard</a>
                <a href="/history.php">History</a>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        <tr><td colspan="11" style="text-align:center;">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Chart -->
    <div id="chartModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3 id="modalTitle" style="margin-top:0;">Performance Graph</h3>
            <canvas id="historyChart"></canvas>
        </div>
    </div>

    <script>
        let modalChart = null;

        document.addEventListener('DOMContentLoaded', async () => {
            const tableBody = document.getElementById('historyTableBody');
            
            try {
                const response = await fetch('/api/get-history.php');
                const result = await response.json();
                
                if (result.status === 'success') {
                    tableBody.innerHTML = '';
                    if (result.data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="11" style="text-align:center;">No history found</td></tr>';
                    } else {
                        result.data.forEach(row => {
                            const tr = document.createElement('tr');
                            
                            // Safely escape response times
                            const rawResponseTimes = row.response_times ? row.response_times : '[]';
                            
                            tr.innerHTML = `
                                <td>${row.id}</td>
                                <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${row.url_target}">${row.url_target}</td>
                                <td>${row.http_method}</td>
                                <td>${row.total_requests}</td>
                                <td>${parseFloat(row.avg_response_time).toFixed(2)} ms</td>
                                <td><span style="color: ${row.success_rate > 90 ? '#03dac6' : '#cf6679'}">${parseFloat(row.success_rate).toFixed(2)}%</span></td>
                                <td style="color: #03dac6">${row.status_2xx}</td>
                                <td style="color: #ffb74d">${row.status_4xx}</td>
                                <td style="color: #cf6679">${row.status_5xx}</td>
                                <td>${new Date(row.created_at).toLocaleString()}</td>
                                <td>
                                    <button class="btn-small" onclick='showChart(${row.id}, "${row.url_target}", ${rawResponseTimes})'>Lihat Grafik</button>
                                </td>
                            `;
                            tableBody.appendChild(tr);
                        });
                    }
                } else {
                    tableBody.innerHTML = `<tr><td colspan="11" style="text-align:center; color: var(--error-color)">Error: ${result.message}</td></tr>`;
                }
            } catch (err) {
                tableBody.innerHTML = `<tr><td colspan="11" style="text-align:center; color: var(--error-color)">Failed to load history</td></tr>`;
            }

            // Modal Logic
            const modal = document.getElementById("chartModal");
            const span = document.getElementsByClassName("close")[0];
            
            span.onclick = function() {
                modal.style.display = "none";
            }
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        });

        function showChart(id, url, responseTimes) {
            const modal = document.getElementById("chartModal");
            document.getElementById("modalTitle").innerText = "Grafik Performa Test #" + id + " - " + url;
            modal.style.display = "block";

            const ctx = document.getElementById('historyChart').getContext('2d');
            
            if (modalChart) {
                modalChart.destroy();
            }

            const labels = responseTimes.map((_, index) => index + 1);

            modalChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Response Time (ms)',
                        data: responseTimes,
                        borderColor: '#03dac6',
                        backgroundColor: 'rgba(3, 218, 198, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            display: true,
                            title: { display: true, text: 'Request Number', color: '#e0e0e0' },
                            ticks: { color: '#e0e0e0' }
                        },
                        y: {
                            display: true,
                            beginAtZero: true,
                            title: { display: true, text: 'Time (ms)', color: '#e0e0e0' },
                            ticks: { color: '#e0e0e0' }
                        }
                    },
                    plugins: {
                        legend: { labels: { color: '#e0e0e0' } }
                    }
                }
            });
        }
    </script>
    <div class="watermark">Created by <strong>adittttttttt123</strong></div>
</body>
</html>
