<?php
class SessionController {

    private static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($iduser, $name, $email, $tipo) {
        self::startSession();
        $_SESSION['id_user'] = $iduser;
        $_SESSION['nome_user'] = $name;
        $_SESSION['email_user'] = $email;
        $_SESSION['tipo_user'] = $tipo;
    }

    public static function logout() {
        self::startSession();
        session_unset();
        session_destroy();
    }

    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['id_user']);
    }

    public static function getUserId() {
        self::startSession();
        return $_SESSION['id_user'] ?? null;
    }

    public static function getUserName() {
        self::startSession();
        return $_SESSION['nome_user'] ?? null;
    }

    public static function getUserTipo() {
        self::startSession();
        return $_SESSION['tipo_user'] ?? 'deslogado';
    }
}
?>
