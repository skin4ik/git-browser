<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Model\Likes;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Github\Client;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{

    protected $likesTable;

    public function indexAction()
    {
        $client = new Client();
        $repositoryInfo = $client->api('repository')->show('yiisoft', 'yii');
        $contributors = $client->api('repository')->contributors('yiisoft', 'yii');
        $likes = [];
        foreach ($contributors as $contributor) {
            if ($this->getLikesTable()->checkTargetOnLike($contributor['id'], Likes::TYPE_LIKE_USER)) {
                $likes[$contributor['id']] = [Likes::STATUS_LIKE];
            } else {
                $likes[$contributor['id']] = [Likes::STATUS_UNLIKE];
            }
        }
        return new ViewModel(array('response' => true, 'repositoryInfo' => $repositoryInfo, 'contributors' => $contributors, 'likes' => $likes));
    }

    public function repositoryInfoAction()
    {
        $username = $this->params()->fromRoute('username');
        $repository = $this->params()->fromRoute('repository');

        if ($username && $repository) {
            $client = new Client();
            $repositoryInfo = $client->api('repository')->show($username, $repository);
            $contributors = $client->api('repository')->contributors($username, $repository);
            $likes = [];
            foreach ($contributors as $contributor) {
                if ($this->getLikesTable()->checkTargetOnLike($contributor['id'], Likes::TYPE_LIKE_USER)) {
                    $likes[$contributor['id']] = [Likes::STATUS_LIKE];
                } else {
                    $likes[$contributor['id']] = [Likes::STATUS_UNLIKE];
                }
            }
            return new ViewModel(array('response' => true, 'repositoryInfo' => $repositoryInfo, 'contributors' => $contributors, 'likes' => $likes));
        }

        return new ViewModel(array('response' => false));

    }

    public function searchRepositoriesAction()
    {
        $query = $this->params()->fromPost('query');
        $likes = [];
        if ($query) {
            $client = new Client();
            $searchResult = $client->api('search')->repositories($query, 'stars');
            foreach ($searchResult['items'] as $repository) {
                if ($this->getLikesTable()->checkTargetOnLike($repository['id'], Likes::TYPE_LIKE_REPOSITORY)) {
                    $likes[$repository['id']] = [Likes::STATUS_LIKE];
                } else {
                    $likes[$repository['id']] = [Likes::STATUS_UNLIKE];
                }
            }
            if ($searchResult) {
                return new ViewModel(array('response' => true, 'searchResult' => $searchResult, 'query' => $query, 'likes' => $likes));
            }
        }

        return new ViewModel(array('response' => false, 'query' => $query));
    }

    public function userInfoAction()
    {
        $login = $this->params()->fromRoute('login');

        if ($login) {
            $client = new Client();
            $userInfo = $client->api('user')->show($login);
            $likes = [];
            if ($this->getLikesTable()->checkTargetOnLike($userInfo['id'], Likes::TYPE_LIKE_USER)) {
                $likes[$userInfo['id']] = [Likes::STATUS_LIKE];
            } else {
                $likes[$userInfo['id']] = [Likes::STATUS_UNLIKE];
            }
            if ($userInfo) {
                return new ViewModel(array('response' => true, 'userInfo' => $userInfo, 'likes'=>$likes));
            }
        }

        return new ViewModel(array('response' => false));
    }

    /**
     * @return mixed
     */
    public function likeAction()
    {
        $data = $this->params()->fromPost('data');
        if (count($data)) {
            $like = new Likes();
            $like->exchangeArray($data);
            if ($this->getLikesTable()->setLike($like)) {
                return new JsonModel(array('response' => true));
            }
        }
        return new JsonModel(array('response' => false));
    }

    public function unLikeAction()
    {
        $data = $this->params()->fromPost('data');
        if (count($data)) {
            if ($this->getLikesTable()->deleteLike($data)) {
                return new JsonModel(array('response' => true));
            }
        }
        return new JsonModel(array('response' => false));
    }

    public function getLikesTable()
    {
        if (!$this->likesTable) {
            $sm = $this->getServiceLocator();
            $this->likesTable = $sm->get('Application\Model\LikesTable');
        }
        return $this->likesTable;
    }
}
