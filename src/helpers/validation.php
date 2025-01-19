<?php

class Validation
{
  public static function validateEmail(string $email): bool
  {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
  }

  public static function validateStringLength(string $str, int $min, int $max): bool
  {
    return strlen($str) >= $min && strlen($str) <= $max;
  }

  public static function validateNumber(int $number, int $min, int $max): bool
  {
    return $number >= $min && $number <= $max;
  }
}