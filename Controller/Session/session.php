<?php
class SessionController {
    public static function login($iduser, $name, $email, $tipo) {
        $_SESSION['id_user'] = $iduser;
        $_SESSION['nome_user'] = $name;
        $_SESSION['email_user'] = $email;
        $_SESSION['tipo_user'] = $tipo;
    }

    public static function logout() {
        session_unset();    
        session_destroy();
    }

    public static function isLoggedIn() {
        return isset($_SESSION['id_user']);
    }

    public static function getUserId() {
        return $_SESSION['id_user'] ?? null;
    }

    public static function getUserName() {  
        return $_SESSION['nome_user'] ?? null;
    }
    
    public static function getUserTipo() { 
        return $_SESSION['tipo_user'] ?? 'deslogado';
    }
}
?>