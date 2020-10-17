<?php

namespace App\Models;

use MF\Model\Model;

class Tweet extends Model {
    private $id;
    private $id_usuario;
    private $tweet;
    private $date;

    public function __get($attr) {
        return $this->$attr;
    }

    public function __set($attr, $valor) {
        $this->$attr = $valor;
    }

    public function registrarTweet() {
        $query = 'insert into tweets(id_usuario, tweet)values(:id_usuario, :tweet)';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':tweet', $this->__get('tweet'));
        $stmt->execute();
    }

    public function recuperarTweets() {
        $query = "
            select
                u.id as id_usuario, t.id as id_tweet, u.name, t.tweet, DATE_FORMAT(t.date, '%d/%m/%Y %H:%i') as date
            from
                tweets as t
                left join usuarios as u on (t.id_usuario = u.id)
                left join usuarios_seguindo as us on (t.id_usuario = us.id_usuario_seguindo)
            where
                t.id_usuario = :id_usuario or us.id_usuario = :id_usuario
            order by
                date desc
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        $tweets = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $tweets;
    }

    public function removerTweet() {
        $query = 'delete from tweets where id = :id_tweet';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_tweet', $this->__get('id'));
        $stmt->execute();
    }
}

?>