<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

    public function index() {
        $this->view->erroLogin = isset($_GET['login']) ? $_GET['login'] : '';

        $this->render('index');
    }

    public function inscreverse() {
        $this->view->erroCadastro = false;

        $this->view->usuario = array(
            'name' => '',
            'email' => '',
            'senha' => '',
        );

        $this->render('inscreverse');
    }

    public function registrar() {
        // logic
        $usuario = Container::getModel('Usuario');

        $usuario->__set('name', $_POST['name']);
        $usuario->__set('email', $_POST['email']);
        $usuario->__set('senha', md5($_POST['senha']));

        if($usuario->validarCadastro() && !$usuario->cadastroExistente()) {
            $usuario->registrar();

            $this->render('cadastro');
        } else {
            $this->view->erroCadastro = true;

            $this->view->usuario = array(
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'senha' => $_POST['senha'],
            );

            $this->render('inscreverse');
        }
    }
}

?>