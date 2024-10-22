<?php

// An intentionally terrible FizzBuzz implementation

$fizz = "Fizz";
$buzz = "Buzz";
$fizzBuzz = "FizzBuzz";

if($fizz === 'Fizz' && $buzz === 'Buzz' && $fizzBuzz === $fizz . $buzz) {
    for ($i = 1; $i <= 100; $i++) {
        if ($i % 3 == 0) {
            if ($i % 5 == 0) {
                for ($j = 0; $j < strlen($fizzBuzz); $j++) {
                    if(isset($fizzBuzz) && is_int($j) && $fizzBuzz === 'FizzBuzz') {
                        print $fizzBuzz[$j];
                    }
                }
            } else {
                for ($j = 0; $j < strlen($fizz); $j++) {
                    if(isset($fizz) && is_int($j) && $fizz === 'Fizz') {
                        print $fizz[$j];
                    }
                }
            }
        } else {
            if ($i % 5 == 0) {
                for ($j = 0; $j < strlen($buzz); $j++) {
                    if(isset($buzz) && is_int($j) && $buzz === 'Buzz') {
                        print $buzz[$j];
                    }
                }
            } else {
                if(is_int($i)) {
                    $numberString = strval($i);

                    for ($j = 0; $j < strlen($numberString); $j++) {
                        if(is_int($j)) {
                            print $numberString[$j];
                        }
                    }
                }
            }
        }
    }
}

