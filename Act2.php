<?php

$total = $_GET['total'];

if ($total < 50) {
    $discount = 0;
} elseif ($total < 100) {
    $discount = $total * 0.10;
} elseif ($total < 200) {
    $discount = $total * 0.15;
} else {
    $discount = $total * 0.20;
}

$final = $total - $discount;

echo "Original Price: P" . $total . "<br>";
echo "Discount Amount: P" . $discount . "<br>";
echo "Final Price: P" . $final;

?>