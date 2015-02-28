<?php
namespace Application\Model;

class Likes
{

    CONST TYPE_LIKE_USER = 'user';
    CONST TYPE_LIKE_REPOSITORY = 'repos';
    CONST STATUS_LIKE = 'like';
    CONST STATUS_UNLIKE = 'unlike';


    public $id;
    public $idTarget;
    public $typeLike;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->idTarget = (isset($data['idTarget'])) ? $data['idTarget'] : null;
        $this->typeLike = (isset($data['typeLike'])) ? $data['typeLike'] : null;
    }
}