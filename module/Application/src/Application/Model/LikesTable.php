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