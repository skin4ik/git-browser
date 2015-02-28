<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class LikesTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param $items
     * @param $likeType
     * @return array
     */
    public function checkOnLikes($items, $likeType)
    {
        $likes = [];
        foreach ($items as $item) {
            if ($this->checkTargetOnLike($item['id'], $likeType)) {
                $likes[$item['id']] = [Likes::STATUS_LIKE];
            } else {
                $likes[$item['id']] = [Likes::STATUS_UNLIKE];
            }
        }
        return $likes;
    }

    public function checkTargetOnLike($idTarget, $typeLike)
    {
        $idTarget = (int)$idTarget;
        $rowset = $this->tableGateway->select(array('id_target' => $idTarget, 'type_like' => $typeLike));
        $row = $rowset->current();
        return $row;
    }

    public function setLike(Likes $dataLike)
    {
        $data = array(
            'id_target' => $dataLike->idTarget,
            'type_like' => $dataLike->typeLike,
        );

        return $this->tableGateway->insert($data);
    }

    public function deleteLike($dataLike)
    {
        return $this->tableGateway->delete(array(
            'id_target' => $dataLike['idTarget'],
            'type_like' => $dataLike['typeLike']
        ));
    }
}