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

namespace L2PClient\Storage;

use Zend\Session\Container;
use L2PClient\Token\AccessToken;
use L2PClient\Token\RefreshToken;
use L2PClient\Token\DeviceToken;

/**
 * Description of ZendSessionStorage
 *
 * @author schurix
 */
class ZendSessionStorage implements StorageInterface{
	
	protected $container;
	
	protected $containerName = 'l2p_api';
	
	public function __construct($containerName = null) {
		if(null !== $containerName){
			$this->setContainerName($containerName);
		}
	}
	
	public function getContainerName() {
		return $this->containerName;
	}

	public function setContainerName($containerName) {
		$this->containerName = $containerName;
		return $this;
	}

	public function getContainer(){
		if(null === $this->container){
			$this->container = new Container($this->getContainerName());
		}
		return $this->container;
	}
	
	public function deleteAccessToken() {
		$this->getContainer()->accessToken = null;
	}

	public function deleteDeviceToken() {
		$this->getContainer()->deviceToken = null;
	}

	public function deleteRefreshToken() {
		$this->getContainer()->refreshToken = null;
	}

	public function getAccessToken() {
		return $this->getContainer()->accessToken;
	}

	public function getDeviceToken() {
		return $this->getContainer()->deviceToken;
	}

	public function getRefreshToken() {
		return $this->getContainer()->refreshToken;
	}

	public function saveAccessToken(AccessToken $accessToken) {
		$this->getContainer()->accessToken = $accessToken;
	}

	public function saveDeviceToken(DeviceToken $deviceToken) {
		$this->getContainer()->deviceToken = $deviceToken;
	}

	public function saveRefreshToken(RefreshToken $refreshToken) {
		$this->getContainer()->refreshToken = $refreshToken;
	}

}
