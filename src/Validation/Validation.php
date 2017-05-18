<?php
namespace LilHermit\Bootstrap4\Validation;

use Cake\I18n\Time;

class Validation {

    public static function datetime($check) {
        try {
            $date = Time::createFromFormat("Y-m-d\\TG:i", $check);
            return $date !== null;
        } catch (\Exception $e) {
        }
        return false;
    }
}