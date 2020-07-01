<?php

function aqua() {
    $random_filled_array = [];
    $array_values = [
        "min" => 0,
        "max" => 100,
        "length" => 100,
        "amount" => 20,
    ];

    for ($i = 0; $i < $array_values["length"]; $i++) {
        $random_filled_array[] = mt_rand($array_values["min"], $array_values["max"]);
    }

    for ($i = 0; $i < $array_values["length"]; $i++)

        for ($j = $array_values["length"] - 1; $j > $i; $j--) {

            $first_number = $random_filled_array[$j];
            $second_number = $random_filled_array[$j - 1];

            if ($first_number > $second_number) {
                $random_filled_array[$j] = $second_number;
                $random_filled_array[$j - 1] = $first_number;
            }

        }

    return array_slice($random_filled_array, 0, $array_values["amount"]);
}

print_r(aqua());

?>