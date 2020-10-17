<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model {
    private $id;
    private $name;
    private $email;
    private $senha;

    public function __get($attr) {
        return $this->$attr;
    }

    public function __set($attr, $valor) {
        $this->$attr = $valor;
    }

    public function registrar() {
        $query = 'insert into usuarios(name, email, senha)values(:name, :email, :senha)';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':email', $this->email);
        $stmt->bindValue(':senha', $this->senha);
        $stmt->execute();
    }

    public function validarCadastro() {
        $isValid = true;

        if(strlen($this->__get('name')) < 3) {
            $isValid = false;
        }
        if(strlen($this->__get('email')) < 5) {
            $isValid = false;
        }
        if(strlen($this->__get('senha')) < 7) {
            $isValid = false;
        }

        return $isValid;
    }

    public function getUsuarioPor($attr) {
        $query = "select * from usuarios where $attr = :$attr";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":$attr", $this->__get($attr));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function cadastroExistente() {
        $nName = count($this->getUsuarioPor('name'));
        $nEmail = count($this->getUsuarioPor('email'));

        return $nName || $nEmail;
    }

    public function autenticarUsuario() {
        $query = 'select id, name, email from usuarios where email = :email and senha = :senha';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        if(!empty($usuario['id']) && !empty($usuario['name'])) {
            $this->__set('id', $usuario['id']);
            $this->__set('name', $usuario['name']);

            return true;
        }

        return false;
    }

    public function searchUsers() {
        $query = 'select id, name, email from usuarios where name like :quem and id != :id';

        $query = "
            select
                u.id, 
                u.name, 
                u.email,
                (
                    select
                        count(*)
                    from
                        usuarios_seguindo as us
                    where
                        us.id_usuario = :id and us.id_usuario_seguindo = u.id
                ) as isfollowed
            from 
                usuarios as u
            where 
                u.name like :quem and u.id != :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':quem', '%'.$this->__get('name').'%');
        $stmt->bindValue(':id', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getUserName() {
        $query = 'select name from usuarios where id = :id_usuario';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTotalTweets() {
        $query = 'select count(*) as total_tweets from tweets where id_usuario = :id_usuario';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTotalFollowing() {
        $query = 'select count(*) as total_following from usuarios_seguindo where id_usuario = :id_usuario';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTotalFollowers() {
        $query = 'select count(*) as total_followers from usuarios_seguindo where id_usuario_seguindo = :id_usuario';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}

?>