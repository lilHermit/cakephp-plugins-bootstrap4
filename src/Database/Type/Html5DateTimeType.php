<?php
namespace lilHermit\Bootstrap4\Database\Type;


use Cake\Database\Type\DateTimeType;
use DateTimeInterface;

class Html5DateTimeType extends DateTimeType {

    /**
     * Parse the datetime from the Html5 format of 2014-12-31T23:59
     *
     * @param mixed $value
     * @return \Cake\I18n\Time|\DateTime|mixed
     */
    public function marshal($value) {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        $class = $this->_className;
        try {
            $date = false;
            if (is_string($value)) {
                $date = $class::createFromFormat("Y-m-d\\TG:i", $value);
            }
            if ($date) {
                return $date;
            }

        } catch (\Exception $e) {
        }

        return parent::marshal($value);
    }
}