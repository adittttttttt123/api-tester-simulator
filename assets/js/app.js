document.addEventListener('DOMContentLoaded', () => {
    const startBtn = document.getElementById('startTest');
    const stopBtn = document.getElementById('stopTest');
    const urlInput = document.getElementById('apiUrl');
    const methodSelect = document.getElementById('method');
    
    let isTesting = false;
    let testInterval;
    
    let totalRequests = 0;
    let successRequests = 0;
    let errorRequests = 0;
    let avgResponseTime = 0;
    let totalResponseTime = 0;
    let status2xx = 0;
    let status4xx = 0;
    let status5xx = 0;
    let responseTimesArray = [];

    if (startBtn) {
        startBtn.addEventListener('click', () => {
            const url = urlInput.value;
            if (!url) {
                alert('Please enter an API URL');
                return;
            }
            
            // Reset metrics on start
            totalRequests = 0;
            successRequests = 0;
            errorRequests = 0;
            avgResponseTime = 0;
            totalResponseTime = 0;
            status2xx = 0;
            status4xx = 0;
            status5xx = 0;
            responseTimesArray = [];
            
            isTesting = true;
            startBtn.disabled = true;
            stopBtn.disabled = false;
            
            testInterval = setInterval(() => performRequest(url, methodSelect.value), 1000); // 1 request per second
        });
    }

    if (stopBtn) {
        stopBtn.addEventListener('click', () => {
            isTesting = false;
            startBtn.disabled = false;
            stopBtn.disabled = true;
            clearInterval(testInterval);
            
            // Simpan hasil ke database
            saveHistoryData(urlInput.value, methodSelect.value);
        });
    }

    async function performRequest(url, method) {
        let statusCode = 0;
        let responseTime = 0;
        
        try {
            const formData = new FormData();
            formData.append('url', url);
            formData.append('method', method);

            const proxyResponse = await fetch('api/proxy.php', {
                method: 'POST',
                body: formData
            });

            if (proxyResponse.ok) {
                const result = await proxyResponse.json();
                statusCode = result.status_code;
                responseTime = result.response_time;
                updateStats(statusCode, responseTime);
            } else {
                statusCode = proxyResponse.status;
                responseTime = 0;
                updateStats(statusCode, 0);
            }
        } catch (error) {
            updateStats(0, 0);
        }

        if (window.updateChart) {
            window.updateChart(responseTime);
        }
    }

    function updateStats(statusCode, responseTime) {
        totalRequests++;
        
        if (statusCode >= 200 && statusCode < 300) {
            successRequests++;
            status2xx++;
        } else if (statusCode >= 400 && statusCode < 500) {
            errorRequests++;
            status4xx++;
        } else if (statusCode >= 500) {
            errorRequests++;
            status5xx++;
        } else {
            errorRequests++; // other errors (like timeout, 0)
        }
        
        responseTimesArray.push(responseTime);
        
        totalResponseTime += responseTime;
        avgResponseTime = totalResponseTime / totalRequests;
        
        document.getElementById('totalRequests').innerText = totalRequests;
        document.getElementById('successRequests').innerText = successRequests;
        document.getElementById('errorRequests').innerText = errorRequests;
        document.getElementById('avgTime').innerText = avgResponseTime.toFixed(2) + ' ms';
    }

    function saveHistoryData(url, method) {
        if (totalRequests === 0) return;
        
        const successRate = (successRequests / totalRequests) * 100;
        
        const testPayload = {
            url_target: url,
            http_method: method,
            total_requests: totalRequests,
            avg_response_time: avgResponseTime,
            success_rate: successRate,
            status_2xx: status2xx,
            status_4xx: status4xx,
            status_5xx: status5xx,
            response_times: responseTimesArray
        };

        fetch('api/save-history.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(testPayload)
        })
        .then(response => response.json())
        .then(data => {
            console.log("Respon Server:", data.message);
            // Tambahkan alert atau notifikasi sukses di UI di sini jika diinginkan
        })
        .catch(error => {
            console.error("Gagal menyimpan riwayat:", error);
        });
    }
});
