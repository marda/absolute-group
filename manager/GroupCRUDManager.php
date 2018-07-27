<?php

namespace Absolute\Module\Group\Manager;

use Absolute\Core\Manager\BaseCRUDManager;

class GroupCRUDManager extends BaseCRUDManager 
{


	public function __construct(\Nette\Database\Context $database) {
  	parent::__construct($database);
	}

	// OTHER METHODS

  // CONNECT METHODS

  public function connectProject($id, $projectId) 
  {
   	$this->database->table('project_group')->where('group_id', $id)->delete();
		return $this->database->table('project_group')->insert(array(
			"group_id" => $id,
			"project_id" => $projectId
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

	public function update($id, $name) 
	{
 		return $this->database->table('group')->where('id', $id)->update(array(
			'name' => $name,
		));
	}
}

