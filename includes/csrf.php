<?php
/**
 * CSRF Protection Helper
 */

class CSRF {
    public static function generateToken($name = 'csrf_token') {
        if (empty($_SESSION[$name])) {
            $_SESSION[$name] = bin2hex(random_bytes(32));
        }
        return $_SESSION[$name];
    }
    
    public static function validateToken($token, $name = 'csrf_token') {
        if (!isset($_SESSION[$name]) || !hash_equals($_SESSION[$name], $token)) {
            return false;
        }
        return true;
    }
    
    public static function getTokenField($name = 'csrf_token') {
        $token = self::generateToken($name);
        return '<input type="hidden" name="' . $name . '" value="' . $token . '">';
    }
}
?>