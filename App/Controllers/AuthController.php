<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action {
    public function autenticar() {
        $usuario = Container::getModel('usuario');

        $usuario->__set('email', $_POST['email']);
        $usuario->__set('senha', md5($_POST['senha']));

        if($usuario->autenticarUsuario()) {
            session_start();

            $_SESSION['twitter_clone_id'] = $usuario->__get('id');
            $_SESSION['twitter_clone_name'] = $usuario->__get('name');

            header('Location: /timeline');
        } else {
            header('Location: /?login=error');
        }

    }

    public function sair() {
        session_start();
        session_destroy();

        header('Location: /');
    }

}

?>