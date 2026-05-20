<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Mengizinkan request jika ada perbedaan origin

// Panggil koneksi database
require_once '../config/database.php';

// Ambil data JSON dari body request
$inputData = file_get_contents("php://input");
$data = json_decode($inputData, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($data)) {
    try {
        // Siapkan query SQL dengan prepared statements (Standar TRPL yang aman)
        $query = "INSERT INTO test_history 
                  (url_target, http_method, total_requests, avg_response_time, success_rate, status_2xx, status_4xx, status_5xx) 
                  VALUES 
                  (:url, :method, :total, :avg_time, :success_rate, :s_2xx, :s_4xx, :s_5xx)";
        
        $stmt = $pdo->prepare($query);
        
        // Bind parameter untuk menghindari SQL Injection
        $stmt->bindParam(':url', $data['url_target']);
        $stmt->bindParam(':method', $data['http_method']);
        $stmt->bindParam(':total', $data['total_requests']);
        $stmt->bindParam(':avg_time', $data['avg_response_time']);
        $stmt->bindParam(':success_rate', $data['success_rate']);
        $stmt->bindParam(':s_2xx', $data['status_2xx']);
        $stmt->bindParam(':s_4xx', $data['status_4xx']);
        $stmt->bindParam(':s_5xx', $data['status_5xx']);
        
        // Eksekusi query
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Riwayat pengujian berhasil disimpan."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal mengeksekusi query."]);
        }
        
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Request tidak valid."]);
}
?>
