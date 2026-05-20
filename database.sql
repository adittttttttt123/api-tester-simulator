CREATE DATABASE IF NOT EXISTS db_api_tester;
USE db_api_tester;

CREATE TABLE IF NOT EXISTS test_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url_target VARCHAR(2048) NOT NULL,
    http_method VARCHAR(10) NOT NULL,
    total_requests INT DEFAULT 0,
    avg_response_time DECIMAL(10,2) DEFAULT 0,
    success_rate DECIMAL(5,2) DEFAULT 0,
    status_2xx INT DEFAULT 0,
    status_4xx INT DEFAULT 0,
    status_5xx INT DEFAULT 0,
    response_times LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
