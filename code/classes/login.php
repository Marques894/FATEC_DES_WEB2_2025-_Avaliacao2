<?php

class Login {
    public function verificar_login() {
        session_start();
        return isset($_SESSION['logado']) && $_SESSION['logado'] === true;
    } 

    public function login($usuario, $senha) {
        session_start();
        if ($usuario === 'admin' && $senha === 'admin') {
            $_SESSION['logado'] = true;
            return true;
        }
        return false;
    }

    public function logout() {
        session_start();
        session_destroy();
    }
}
