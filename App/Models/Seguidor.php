<?php

namespace App\Models;

use MF\Model\Model;

class Seguidor extends Model {
    private $id;
    private $id_usuario;
    private $id_usuario_seguindo;

    public function __get($attr) {
        return $this->$attr;
    }

    public function __set($attr, $valor) {
        $this->$attr = $valor;
    }

    public function follow() {
        $query = 'insert into usuarios_seguindo(id_usuario, id_usuario_seguindo)values(:id_usuario, :id_usuario_seguindo)';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':id_usuario_seguindo', $this->__get('id_usuario_seguindo'));
        $stmt->execute();
    }

    public function unfollow() {
        $query = 'delete from usuarios_seguindo where id_usuario = :id_usuario and id_usuario_seguindo = :id_usuario_seguindo';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':id_usuario_seguindo', $this->__get('id_usuario_seguindo'));
        $stmt->execute();
    }
}

?>