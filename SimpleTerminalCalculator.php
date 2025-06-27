<?php
declare(strict_types=1);

/**
 * Simple Terminal Calculator
 * Accepts a number of expressions and prints formatted results.
 */

if (!isset($argv[1]) || !is_numeric($argv[1]) || (int) $argv[1] <= 0) {
    echo "Usage: php SimpleTerminalCalculator.php <number_of_expressions>\n";
    echo "Example: php SimpleTerminalCalculator.php 3\n";
    echo "This script expects a number of expressions to be provided as input.\n";
    echo "Each expression should be in the format: <number1><operator><number2>\n";
    echo "Operators can be +, - or *.\n";
    exit(1);
}

function printGap(int $length): void {
    $length = ($length > 0) ? $length : 0;
    echo str_repeat(' ', $length);
}

function printLine(int $length): void {
    $length = ($length > 0) ? $length : 0;
    echo str_repeat('-', $length);
}

function printMultiplicationSubtotals(string $n1, string $n2, int $maxLength): void {
    for ($i=0; $i < strlen($n2); $i++) {

        $subTotal = bcmul($n1, $n2[(strlen($n2) - $i - 1)]);

        printGap($maxLength - $i - strlen($subTotal));
        echo $subTotal . "\n";
    }
}

function isMultiplication(string $operator): bool {
    return $operator === '*';
}

function printOperation(string $n1, string $n2, string $operator, callable $operation): void {
    $res = $operation($n1, $n2);

    $n1Length = strlen($n1);
    $n2Length = strlen($n2) + 1; // this line includes $operator
    $resLength = strlen($res);

    $maxLength = max($n1Length, $n2Length, $resLength);

    // print first number
    printGap($maxLength - $n1Length);
    echo $n1 . "\n";

    // print second number
    printGap($maxLength - $n2Length);
    echo $operator . $n2 . "\n";

    // print resulting line
    if (isMultiplication($operator)) {
        $inputMaxLength = max($n1Length, $n2Length);
        printGap($maxLength - $inputMaxLength);
        printLine($inputMaxLength);
    } else {
        printLine($maxLength);
    }
    echo "\n";

    if (isMultiplication($operator) && strlen($n2) > 1) {
        printMultiplicationSubtotals($n1, $n2, $maxLength);
        // print result line
        printLine($maxLength);
        echo "\n";
    }

    printGap($maxLength - $resLength);
    echo $res . "\n\n";
}

function printSubtraction(string $n1, string $n2): void {
    printOperation($n1, $n2, '-', fn($a, $b) => bcsub($a, $b));
}

function printAddition(string $n1, string $n2): void {
    printOperation($n1, $n2, '+', fn($a, $b) => bcadd($a, $b));
}

function printMultiplication(string $n1, string $n2): void {
    printOperation($n1, $n2, '*', fn($a, $b) => bcmul($a, $b));
}

$expressionCount = (int) $argv[1];
$expressions = [];

// read inputs
for ($i=0; $i < $expressionCount; $i++) { 
    $expression = readline();
    if (empty($expression)) {
        throw new Exception("Error - empty expression", 1);
    }
    $expression = trim($expression);

    if (!preg_match('/^(\d+)([+\-*\/])(\d+)$/', $expression, $matches)) {
        throw new Exception("Error - invalid expression", 1);
    }
    $expressions[] = [
        'firstNumber' => $matches[1],
        'operator' => $matches[2],
        'secondNumber' => $matches[3]
    ];
}
echo "\n";

// output
foreach ($expressions as $expression) {
    $firstNumber = $expression['firstNumber'];
    $operator = $expression['operator'];
    $secondNumber = $expression['secondNumber'];
    
    match($operator) {
        '*' => printMultiplication($firstNumber, $secondNumber),
        '+' => printAddition($firstNumber, $secondNumber),
        '-' => printSubtraction($firstNumber, $secondNumber),
        default => 'invalid operator: ' . $operator
    };
    echo "\n";
}

echo "finished running.\n" ;
echo "solved " . $expressionCount . " expressions\n" ;