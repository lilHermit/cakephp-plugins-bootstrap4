<?php

namespace lilHermit\Bootstrap4\Configure;

class Assets {

    public static function css() {
        return [
            '4.0.0-alpha.5' => [
                'href' => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css',
                'integrity' => 'sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi'
            ],
            '4.0.0-alpha.6' => [
                'href' => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css',
                'integrity' => 'sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ'
            ]
        ];
    }

    public static function javascript() {
        return [
            '4.0.0-alpha.2' => [
                'src' => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js',
                'integrity' => 'sha384-vZ2WRJMwsjRMW/8U7i6PWi6AlO1L79snBrmgiDpgIWJ82z8eA5lenwvxbMV1PAh7'
            ],
            '4.0.0-alpha.5' => [
                'src' => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js',
                'integrity' => 'sha384-BLiI7JTZm+JWlgKa0M0kGRpJbF2J8q+qreVrKBC47e3K6BW78kGLrCkeRX6I9RoK'
            ],
            '4.0.0-alpha.6' => [
                'src' => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js',
                'integrity' => 'sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn'
            ]
        ];
    }
}