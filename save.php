<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type");

$host = 'sql12.freesqldatabase.com'; //
$db   = 'sql12825158';
$user = 'sql12825158';
$pass = 'lCgrDaY426';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed"]));
}

// FETCH DATA (GET REQUEST)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT * FROM enrollments ORDER BY id DESC");
    $rows = [];
    while($r = $result->fetch_assoc()) {
        $rows[] = $r;
    }
    echo json_encode($rows);
} 

// SAVE DATA (POST REQUEST)
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if ($data) {
        $stmt = $conn->prepare("INSERT INTO enrollments (form_no, enroll_date, full_name, gender, perm_address, temp_address, email, phone, level, faculty, year_sem, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $name = str_replace('.', '', $data['name']);
        
        $stmt->bind_param("ssssssssssss", 
            $data['fno'], $data['date'], $name, $data['gender'], 
            $data['padd'], $data['tadd'], $data['email'], $data['phone'], 
            $data['lvl'], $data['fac'], $data['yr'], $data['photo']
        );

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => $stmt->error]);
        }
        $stmt->close();
    }
}
$conn->close();
?>
