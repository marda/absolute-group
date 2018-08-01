<?php

namespace Absolute\Module\Group\Presenter;

use Nette\Http\Response;
use Nette\Application\Responses\JsonResponse;
use Absolute\Module\Group\Presenter\LabaleBasePresenter;

class DefaultPresenter extends GroupBasePresenter
{

    /** @var \Absolute\Module\Group\Manager\GroupCRUDManager @inject */
    public $groupCRUDManager;

    /** @var \Absolute\Module\Group\Manager\GroupManager @inject */
    public $groupManager;

    public function startup()
    {
        parent::startup();
    }

    public function renderDefault($resourceId)
    {
        switch ($this->httpRequest->getMethod())
        {
            case 'GET':
                if ($resourceId != null)
                {
                    $this->_getRequest($resourceId);
                }
                else
                {
                    $this->_getListRequest($this->getParameter('offset'), $this->getParameter('limit'));
                }
                break;
            case 'POST':
                $this->_postRequest($resourceId);
                break;
            case 'PUT':
                $this->_putRequest($resourceId);
                break;
            case 'DELETE':
                $this->_deleteRequest($resourceId);
            default:

                break;
        }
        $this->sendResponse(new JsonResponse(
                $this->jsonResponse->toJson(), "application/json;charset=utf-8"
        ));
    }

    private function _getRequest($id)
    {
        //if ($this->groupManager->canUserEdit($this->user->id, $id))
        {
            $group = $this->groupManager->getById($id);
            if (!$group)
            {
                $this->httpResponse->setCode(Response::S404_NOT_FOUND);
                return;
            }
            $this->jsonResponse->payload = $group->toJson();
            $this->httpResponse->setCode(Response::S200_OK);
        }
        //else
        //    $this->httpResponse->setCode(Response::S403_FORBIDDEN);
    }

    private function _getListRequest($offset, $limit)
    {
        $groups = $this->groupManager->_getList( $offset, $limit);
        $this->httpResponse->setCode(Response::S200_OK);

        $this->jsonResponse->payload = array_map(function($n)
        {
            return $n->toJson();
        }, $groups);
    }

    private function _putRequest($id)
    {
        $post = json_decode($this->httpRequest->getRawBody(), true);
        if ($this->groupManager->canUserEdit($id, $this->user->id))
        {
            if (isset($post['name']))
            {
                $res = $this->groupCRUDManager->update($id, $post);
                if (!$res)
                    $this->httpResponse->setCode(Response::S500_INTERNAL_SERVER_ERROR);
            }
            else
                $this->httpResponse->setCode(Response::S400_BAD_REQUEST);
        }
        else
        {
            $this->jsonResponse->payload = [];
            $this->httpResponse->setCode(Response::S403_FORBIDDEN);
        }
    }

    private function _postRequest($urlId)
    {
        $post = json_decode($this->httpRequest->getRawBody(), true);
        if (!isset($post['name']))
            $this->httpResponse->setCode(Response::S400_BAD_REQUEST);
        else
        {
            $ret = $this->groupCRUDManager->create($this->user->id, $post['name']);
            if (!$ret)
            {
                $this->jsonResponse->payload = [];
                $this->httpResponse->setCode(Response::S500_INTERNAL_SERVER_ERROR);
            }
            else
            {
                if(isset($post['users']))
                    $this->groupCRUDManager->connectUsers ($ret, $post['users']);
                
                if (isset($post["projects"]))
                    $this->groupCRUDManager->connectProjects ($ret, $post["projects"]);
                
                $this->jsonResponse->payload = [];
                $this->httpResponse->setCode(Response::S201_CREATED);
            }
        }
    }

    private function _deleteRequest($id)
    {
        if ($this->groupManager->canUserEdit($id, $this->user->id))
        {
            $this->groupCRUDManager->delete($id);
            $this->httpResponse->setCode(Response::S200_OK);
        }
    }

}
