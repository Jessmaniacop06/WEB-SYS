<?php

if (isset($_GET['number'])) {

    $number = (int) $_GET['number'];

    echo "<h2>Number Checker</h2>";
    echo "Number: " . $number . "<br>";

    if ($number > 0) {
        echo "Result: Positive<br>";

        if ($number % 2 == 0) {
            echo "The number is Even.";
        } else {
            echo "The number is Odd.";
        }

    } elseif ($number < 0) {
        echo "Result: Negative";
    } else {
        echo "Result: Zero";
    }

} else {
    echo "Please provide a number in the URL.";
}
?>