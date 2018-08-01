<?php

namespace Absolute\Module\Group\Manager;

use Absolute\Core\Manager\BaseCRUDManager;

class GroupCRUDManager extends BaseCRUDManager
{

    public function __construct(\Nette\Database\Context $database)
    {
        parent::__construct($database);
    }

    // OTHER METHODS
    // CONNECT METHODS

    public function connectUsers($id, $users)
    {
        $users = array_unique(array_filter($users));
        // DELETE
        $this->database->table('group_user')->where('group_id', $id)->delete();
        // INSERT NEW
        $data = [];
        foreach ($users as $userId)
        {
            $data[] = array(
                "group_id" => $id,
                "user_id" => $userId,
            );
        }

        if (!empty($data))
        {
            $this->database->table('group_user')->insert($data);
        }
        return true;
    }

    public function connectProjects($id, $projects)
    {
        $projects = array_unique(array_filter($projects));
        // DELETE
        $this->database->table('project_group')->where('group_id', $id)->delete();
        // INSERT NEW
        $data = [];
        foreach ($projects as $projectId)
        {
            $data[] = array(
                "group_id" => $id,
                "project_id" => $projectId,
            );
        }

        if (!empty($data))
        {
            $this->database->table('project_group')->insert($data);
        }
        return true;
    }

    public function connectProject($id, $projectId)
    {
        $this->database->table('group_user')->where('group_id', $id)->delete();
        return $this->database->table('group_user')->insert(array(
                    "group_id" => $id,
                    "user_id" => $projectId
        ));
    }

    // CUD METHODS

    public function create($userId, $name)
    {
        $result = $this->database->table('group')->insert(array(
            'user_id' => $userId,
            'name' => $name,
            'created' => new \DateTime(),
        ));
        return $result;
    }

    public function delete($id)
    {
        $this->database->table('project_group')->where('group_id', $id)->delete();
        $this->database->table('group_user')->where('group_id', $id)->delete();
        return $this->database->table('group')->where('id', $id)->delete();
    }

    public function update($id, $post)
    {
        if (isset($post["projects"]))
            $this->connectProjects ($id, $post["projects"]);
        
        if (isset($post["users"]))
            $this->connectUsers ($id, $post["users"]);
        
        return $this->database->table('group')->where('id', $id)->update(array(
                    'name' => $post["name"],
        ));
    }

}
