<?php

/*
 * Copyright (C) 2016 schurix
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace L2PClient\Token;
use DateTime;
use JsonSerializable;

/**
 * Description of Token
 *
 * @author schurix
 */
class Token implements JsonSerializable{
	
	/**
	 * Just some ID that can be passed to reidentify the token
	 * @var int
	 */
	protected $id;
	
	/**
	 * Id of the user owning this token.
	 * @var int
	 */
	protected $userId;
	
	/**
	 * @var DateTime
	 */
	protected $issueTime;
	
	public function __construct(DateTime $issueTime, $id = null, $userId = null) {
		$this->setIssueTime($issueTime);
		$this->setId($id);
		$this->setUserId($userId);
	}
	
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	public function getUserId() {
		return $this->userId;
	}

	public function setUserId($userId) {
		$this->userId = $userId;
		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getIssueTime() {
		return $this->issueTime;
	}

	/**
	 * @param DateTime $issueTime
	 * @return Token
	 */
	public function setIssueTime($issueTime) {
		$this->issueTime = $issueTime;
		return $this;
	}

	public function jsonSerialize() {
		return array(
			'issueTime' => $this->getIssueTime()->format(DateTime::ATOM),
		);
	}
	
	public function __sleep() {
		return array_keys($this->jsonSerialize());
	}

}
