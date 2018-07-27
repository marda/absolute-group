<?php

namespace App\Classes;

namespace Absolute\Module\Group\Entity;

class Group {

  private $id;
  private $name;
  private $created;
  private $userId;

	public function __construct($id, $userId, $name, $created) {
    $this->id = $id;
		$this->name = $name;
    $this->created = $created;
    $this->userId = $userId;
	}

  public function getId() {
    return $this->id;
  }

  public function getUserId() {
    return $this->userId;
  }

  public function getName() {
    return $this->name;
  }

  public function getCreated() {
    return $this->created;
  }

  // SETTERS

  // ADDERS

  // OTHER METHODS  

  public function toJson() {
    return array(
      "id" => $this->id,
      "name" => $this->name,
    );
  }
}

