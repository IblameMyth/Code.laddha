<?php
header('Content-Type: application/json');

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

// Load existing XML file
$xml = simplexml_load_file('expenses.xml');
if (!$xml) {
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><monthlyExpenses></monthlyExpenses>');
}

// Get current month ID (YYYY-MM)
$monthId = date('Y-m');

// Find or create month node
$month = null;
foreach ($xml->month as $m) {
    if ((string)$m['id'] === $monthId) {
        $month = $m;
        break;
    }
}
if (!$month) {
    $month = $xml->addChild('month');
    $month->addAttribute('id', $monthId);
    $month->addChild('dailyExpenses');
    $month->addChild('futureExpenses');
    $month->addChild('categories');
}

// Add expense based on type
if ($data['type'] === 'daily') {
    $expense = $month->dailyExpenses->addChild('expense');
    $expense->addChild('date', $data['date']);
    $expense->addChild('food', $data['food']);
    $expense->addChild('travel', $data['travel']);
    $expense->addChild('misc', $data['misc']);
    $expense->addChild('total', $data['total']);
} else if ($data['type'] === 'future') {
    $expense = $month->futureExpenses->addChild('expense');
    $expense->addChild('name', $data['name']);
    $expense->addChild('amount', $data['amount']);
    $expense->addChild('date', $data['date']);
}

// Save the XML file
$xml->asXML('expenses.xml');

// Return success response
echo json_encode(['status' => 'success']);
?>