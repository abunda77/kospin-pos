<?php

// Function to get input from terminal
function getInput($prompt) {
    echo $prompt . ": ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    return trim($line);
}

echo "--- Generate Signature Key ---\n";

$orderId = getInput("Enter Order ID (e.g., 000004)");
$statusCode = getInput("Enter Status Code (e.g., 200)");
$grossAmount = getInput("Enter Gross Amount (e.g., 3500.00)");
$serverKey = getInput("Enter Server Key");

// Ensure gross amount is properly formatted if needed, but for now trust user input or just trim
// Some gateways require .00, some don't. The original script had "3500.00"

$input = $orderId . $statusCode . $grossAmount . $serverKey;
$signature = openssl_digest($input, 'sha512');

echo "\n--------------------------\n";
echo "INPUT STRING: " . $input . "\n";
echo "SIGNATURE   : " . $signature . "\n";
echo "--------------------------\n";
?>