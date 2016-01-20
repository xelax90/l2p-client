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

/**
 * Description of AccessToken
 *
 * @author schurix
 */
class AccessToken extends Token{
	protected $accessToken;
	
	protected $tokenType;
	
	protected $expiresIn;
	
	public function __construct($issueTime, $accessToken, $tokenType, $expiresIn) {
		parent::__construct($issueTime);
		$this->accessToken = $accessToken;
		$this->tokenType = $tokenType;
		$this->expiresIn = $expiresIn;
	}

	public function getAccessToken() {
		return $this->accessToken;
	}

	public function getTokenType() {
		return $this->tokenType;
	}

	public function getExpiresIn() {
		return $this->expiresIn;
	}

	public function setAccessToken($accessToken) {
		$this->accessToken = $accessToken;
		return $this;
	}

	public function setTokenType($tokenType) {
		$this->tokenType = $tokenType;
		return $this;
	}

	public function setExpiresIn($expiresIn) {
		$this->expiresIn = $expiresIn;
		return $this;
	}

	public function isExpired(){
		$time = clone $this->getIssueTime();
		$time->modify('+'.$this->getExpiresIn().' seconds');
		$now = new DateTime();
		return $now > $time;
	}
	
	public function jsonSerialize() {
		$data = parent::jsonSerialize();
		return array_merge($data, array(
			'accessToken' => $this->getAccessToken(),
			'tokenType' => $this->getTokenType(),
			'expiresIn' => $this->getExpiresIn(),
		));
	}
}
