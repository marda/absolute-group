<?php

namespace Absolute\Module\Group\Manager;
use Absolute\Module\Group\Entity\Group;
use Absolute\Core\Manager\BaseManager;

class GroupManager extends BaseManager
{

    public function __construct(\Nette\Database\Context $database)
    {
        parent::__construct($database);
    }

    /* INTERNAL/EXTERNAL INTERFACE */

    protected function _getGroup($db)
    {
        if ($db == false)
        {
            return false;
        }
        $object = new Group($db->id, $db->user_id, $db->name, $db->created);
        return $object;
    }

    public function _getById($id)
    {
        $resultDb = $this->database->table('group')->get($id);
        return $this->_getGroup($resultDb);
    }

    private function _getUserList($userId, $offset, $limit)
    {
        $ret = array();
        $resultDb = $this->database->table('group')->where('user_id', $userId)->limit($limit, $offset);
        foreach ($resultDb as $db)
        {
            $object = $this->_getGroup($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getList($offset, $limit)
    {
        $ret = array();
        $resultDb = $this->database->table('group')->limit($limit, $offset);
        foreach ($resultDb as $db)
        {
            $object = $this->_getGroup($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _canUserEdit($id, $userId)
    {
        $db = $this->database->table('group')->get($id);
        if (!$db)
        {
            return false;
        }
        if ($db->user_id === $userId)
        {
            return true;
        }
        $projectsInManagement = $this->database->table('project_user')->where('user_id', $userId)->where('role', array('owner', 'manager'))->fetchPairs('project_id', 'project_id');
        $projects = $this->database->table('project_group')->where('group_id', $id)->fetchPairs('project_id', 'project_id');
        return (!empty(array_intersect($projects, $projectsInManagement))) ? true : false;
    }

    private function _getProjectList($projectId)
    {
        $ret = array();
        $resultDb = $this->database->table('group')->where(':project_group.project_id', $projectId);
        foreach ($resultDb as $db)
        {
            $object = $this->_getGroup($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getProjectItem($projectId, $groupId)
    {
        return $this->_getGroup($this->database->table('group')->where(':project_group.project_id', $projectId)->where("group_id", $groupId)->fetch());
    }

    public function _groupProjectDelete($projectId, $groupId)
    {
        return $this->database->table('project_group')->where('project_id', $projectId)->where('group_id', $groupId)->delete();
    }

    public function _groupProjectCreate($projectId, $groupId)
    {
        return $this->database->table('project_group')->insert(['project_id' => $projectId, 'group_id' => $groupId]);
    }

    /* EXTERNAL METHOD */

    public function getProjectList($projectId)
    {
        return $this->_getProjectList($projectId);
    }

    public function getProjectItem($projectId, $groupId)
    {
        return $this->_getProjectItem($projectId, $groupId);
    }

    public function groupProjectDelete($projectId, $groupId)
    {
        return $this->_groupProjectDelete($projectId, $groupId);
    }

    public function groupProjectCreate($projectId, $groupId)
    {
        return $this->_groupProjectCreate($projectId, $groupId);
    }

    public function getById($id)
    {
        return $this->_getById($id);
    }

    public function getList($userId)
    {
        return $this->_getList($userId);
    }

    public function getUserList($userId, $offset, $limit)
    {
        return $this->_getUserList($userId, $offset, $limit);
    }

    public function canUserEdit($id, $userId)
    {
        return $this->_canUserEdit($id, $userId);
    }

}
