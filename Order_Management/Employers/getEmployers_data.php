<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../backend/db.php");

if (session_status() === PHP_SESSION_NONE) session_start();

// Admin-only
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    http_response_code(403);
    echo json_encode(['success'=>false,'message'=>'Access denied']);
    exit;
}

header('Content-Type: application/json');
$action = $_POST['action'] ?? 'fetch';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---------- ADD EMPLOYER ----------
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $role = trim($_POST['role'] ?? 'employer');

        // Check if email exists
        $stmtCheck = $conn->prepare("SELECT user_id FROM users WHERE email=?");
        $stmtCheck->bind_param("s", $email);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        if($stmtCheck->num_rows > 0){
            echo json_encode(['success'=>false,'message'=>'Email already exists']);
            exit;
        }

        // Insert into users
        $stmt = $conn->prepare("INSERT INTO users (name,email,password,role,created_at) VALUES (?,?,?,?,NOW())");
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        if($stmt->execute()){
            $user_id = $stmt->insert_id;

            // Insert into employers
            $stmt2 = $conn->prepare("INSERT INTO employers (user_id, phone, address) VALUES (?,?,?)");
            $stmt2->bind_param("iss", $user_id, $phone, $address);
            if($stmt2->execute()){
                echo json_encode(['success'=>true,'message'=>'Employer added successfully']);
            } else {
                echo json_encode(['success'=>false,'message'=>$conn->error]);
            }
        } else {
            echo json_encode(['success'=>false,'message'=>$conn->error]);
        }
        exit;
    }

    // ---------- UPDATE EMPLOYER ----------
    if ($action === 'update') {
        $employer_id = intval($_POST['employer_id']);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $role = trim($_POST['role'] ?? 'employer');

        // Get user_id
        $stmt = $conn->prepare("SELECT user_id FROM employers WHERE employer_id=?");
        $stmt->bind_param("i",$employer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($row = $result->fetch_assoc()){
            $user_id = $row['user_id'];

            // Update users table
            if(!empty($password)){
                $stmt2 = $conn->prepare("UPDATE users SET name=?, email=?, password=?, role=? WHERE user_id=?");
                $stmt2->bind_param("ssssi", $name, $email, $password, $role, $user_id);
            } else {
                $stmt2 = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE user_id=?");
                $stmt2->bind_param("sssi", $name, $email, $role, $user_id);
            }
            $stmt2->execute();

            // Update employers table
            $stmt3 = $conn->prepare("UPDATE employers SET phone=?, address=? WHERE employer_id=?");
            $stmt3->bind_param("ssi", $phone, $address, $employer_id);
            $stmt3->execute();

            echo json_encode(['success'=>true,'message'=>'Employer updated successfully']);
        } else {
            echo json_encode(['success'=>false,'message'=>'Employer not found']);
        }
        exit;
    }

    // ---------- DELETE EMPLOYER ----------
    if ($action === 'delete') {
        $employer_id = intval($_POST['employer_id']);
        $stmt = $conn->prepare("SELECT user_id FROM employers WHERE employer_id=?");
        $stmt->bind_param("i",$employer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($row = $result->fetch_assoc()){
            $user_id = $row['user_id'];
            // Delete user (ON DELETE CASCADE will remove employer row)
            $stmt2 = $conn->prepare("DELETE FROM users WHERE user_id=?");
            $stmt2->bind_param("i",$user_id);
            $stmt2->execute();
            echo json_encode(['success'=>true,'message'=>'Employer deleted successfully']);
        } else {
            echo json_encode(['success'=>false,'message'=>'Employer not found']);
        }
        exit;
    }
}

// ---------- FETCH EMPLOYERS ----------
$sql = "SELECT e.employer_id, u.name, u.email, u.role, u.created_at, e.phone, e.address 
        FROM employers e 
        JOIN users u ON e.user_id = u.user_id
        ORDER BY e.employer_id DESC";
$result = $conn->query($sql);

$employers = [];
if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()) $employers[] = $row;
}

echo json_encode($employers);
