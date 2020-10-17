<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {
    public function timeline() {
        $this->autenticarUsuario();

        $tweet = Container::getModel('Tweet');
        $tweet->__set('id_usuario', $_SESSION['twitter_clone_id']);

        $this->view->tweets = $tweet->recuperarTweets();

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['twitter_clone_id']);
        
        $this->view->userName = $usuario->getUserName();
        $this->view->totalTweets = $usuario->getTotalTweets();
        $this->view->totalFollowing = $usuario->getTotalFollowing();
        $this->view->totalFollowers = $usuario->getTotalFollowers();

        $this->render('timeline');
    }

    public function tweet() {
        $this->autenticarUsuario();

        if(isset($_POST['tweet']) && $_POST['tweet'] != '') {
            $tweet = Container::getModel('Tweet');

            $tweet->__set('id_usuario', $_SESSION['twitter_clone_id']);
            $tweet->__set('tweet', $_POST['tweet']);

            $tweet->registrarTweet();
        }

        header('Location: /timeline');
    }

    public function quemSeguir() {
        $this->autenticarUsuario();

        $quem = isset($_POST['quem']) ? $_POST['quem'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['twitter_clone_id']);

        $this->view->userName = $usuario->getUserName();
        $this->view->totalTweets = $usuario->getTotalTweets();
        $this->view->totalFollowing = $usuario->getTotalFollowing();
        $this->view->totalFollowers = $usuario->getTotalFollowers();

        $usuarios = array();

        if($quem != '') {
            $usuario = Container::getModel('Usuario');

            $usuario->__set('name', $quem);
            $usuarios = $usuario->searchUsers();
        }

        $this->view->usuariosPesquisa = $usuarios;

        $this->render('quemSeguir');
    }

    public function action() {
        $this->autenticarUsuario();

        $action = isset($_GET['action']) ? $_GET['action'] : '';

        $id = isset($_GET['id']) ? $_GET['id'] : '';  
        $isfollowed = isset($_GET['isfollowed']) ? $_GET['isfollowed'] : ''; 

        $seguidor = Container::getModel('Seguidor');

        $seguidor->__set('id_usuario', $_SESSION['twitter_clone_id']);
        $seguidor->__set('id_usuario_seguindo', $id);

        if($action == 'follow' && $isfollowed == false) {
            $seguidor->follow();

        } else if($action == 'unfollow' && $isfollowed == true) {
            $seguidor->unfollow();

        }

        header('Location: /quem_seguir');
    }

    public function remove() {
        $this->autenticarUsuario();

        $tweet = Container::getModel('tweet');

        $tweet->__set('id', $_POST['id_tweet']);
        $tweet->removerTweet();

        header('Location: /timeline');
    }

    public function autenticarUsuario() {
        session_start();

        if(!isset($_SESSION['twitter_clone_id']) || $_SESSION['twitter_clone_id'] == '') {
            header('Location: /');
        }
        if(!isset($_SESSION['twitter_clone_name']) || $_SESSION['twitter_clone_name'] == '') {
            header('Location: /');
        }
    }
}

?>