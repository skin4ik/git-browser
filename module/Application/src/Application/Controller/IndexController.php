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
    /**
     * @var
     */
    protected $likesTable;

    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $client = new Client();
        $repositoryInfo = $client->api('repository')->show('yiisoft', 'yii');
        $contributors = $client->api('repository')->contributors('yiisoft', 'yii');
        $likes = $this->getLikesTable()->checkOnLikes($contributors, Likes::TYPE_LIKE_USER);
        return new ViewModel(array('response' => true, 'repositoryInfo' => $repositoryInfo, 'contributors' => $contributors, 'likes' => $likes));
    }

    /**
     * @return ViewModel
     */
    public function repositoryInfoAction()
    {
        $username = $this->params()->fromRoute('username');
        $repository = $this->params()->fromRoute('repository');

        if ($username && $repository) {
            $client = new Client();
            $repositoryInfo = $client->api('repository')->show($username, $repository);
            $contributors = $client->api('repository')->contributors($username, $repository);
            $likes = $this->getLikesTable()->checkOnLikes($contributors, Likes::TYPE_LIKE_USER);
            return new ViewModel(array('response' => true, 'repositoryInfo' => $repositoryInfo, 'contributors' => $contributors, 'likes' => $likes));
        }

        return new ViewModel(array('response' => false));

    }

    /**
     * @return ViewModel
     */
    public function searchRepositoriesAction()
    {
        $query = $this->params()->fromPost('query');
        if ($query) {
            $client = new Client();
            $searchResult = $client->api('search')->repositories($query, 'stars');
            $likes = $this->getLikesTable()->checkOnLikes($searchResult['items'], Likes::TYPE_LIKE_REPOSITORY);
            if ($searchResult) {
                return new ViewModel(array('response' => true, 'searchResult' => $searchResult, 'query' => $query, 'likes' => $likes));
            }
        }

        return new ViewModel(array('response' => false, 'query' => $query));
    }

    /**
     * @return ViewModel
     */
    public function userInfoAction()
    {
        $login = $this->params()->fromRoute('login');

        if ($login) {
            $client = new Client();
            $userInfo = $client->api('user')->show($login);
            $likes = $this->getLikesTable()->checkOnLikes(array($userInfo), Likes::TYPE_LIKE_USER);
            if ($userInfo) {
                return new ViewModel(array('response' => true, 'userInfo' => $userInfo, 'likes' => $likes));
            }
        }

        return new ViewModel(array('response' => false));
    }

    /**
     * @return JsonModel
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

    /**
     * @return JsonModel
     */
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

    /**
     * @return array|object
     */
    public function getLikesTable()
    {
        if (!$this->likesTable) {
            $sm = $this->getServiceLocator();
            $this->likesTable = $sm->get('Application\Model\LikesTable');
        }
        return $this->likesTable;
    }
}
