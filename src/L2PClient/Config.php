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

namespace L2PClient;

use L2PClient\Storage\StorageInterface;

/**
 * L2P API Configuration
 *
 * @author schurix
 */
class Config {
	
	/**
	 * URL to OAuth server
	 * @var string
	 */
	protected $authUrl = 'https://oauth.campus.rwth-aachen.de/';
	
	/**
	 * Client id provided by IT Center
	 * @var string
	 */
	protected $clientId = '';
	
	/**
	 * @var string
	 */
	protected $scope = 'l2p2013.rwth';
	
	/**
	 * Token storage
	 * @var StorageInterface
	 */
	protected $storage;
	
	/**
	 * L2P API server
	 * @var string
	 */
	protected $apiUrl = 'https://www3.elearning.rwth-aachen.de/_vti_bin/l2pservices/api.svc/v1/';
	
	public function __construct(StorageInterface $storage, $clientId) {
		$this->setStorage($storage);
		$this->setClientId($clientId);
	}
	
	/**
	 * Returns URL to OAuth server. Always ends with '/'.
	 * @return string
	 */
	public function getAuthUrl() {
		return $this->authUrl;
	}
	
	/**
	 * Return Client ID provided by IT Center
	 * @return string
	 */
	public function getClientId() {
		return $this->clientId;
	}
	
	/**
	 * Returns token scope
	 * @return string
	 */
	public function getScope() {
		return $this->scope;
	}
	
	/**
	 * Returns token storage
	 * @return StorageInterface
	 */
	public function getStorage() {
		return $this->storage;
	}
	
	/**
	 * Sets the OAuth server URL. Returns self for fluent setters
	 * @param string $authUrl
	 * @return Config
	 */
	public function setAuthUrl($authUrl) {
		if(substr($authUrl, -1) !== '/'){
			$authUrl .= '/';
		}
		$this->authUrl = $authUrl;
		return $this;
	}

	/**
	 * Sets the client ID provided by IT Ceter. Returns self for fluent setters
	 * @param string $clientId
	 * @return Config
	 */
	public function setClientId($clientId) {
		$this->clientId = $clientId;
		return $this;
	}

	/**
	 * Sets the token scope. Returns self for fluent setters
	 * @param string $scope
	 * @return Config
	 */
	public function setScope($scope) {
		$this->scope = $scope;
		return $this;
	}

	/**
	 * Sets the token storage. Returns self for fluent setters
	 * @param StorageInterface $storage
	 * @return Config
	 */
	public function setStorage(StorageInterface $storage) {
		$this->storage = $storage;
		return $this;
	}
	
	/**
	 * Returns L2P API server. Always ends with '/'.
	 * @return string
	 */
	public function getApiUrl() {
		return $this->apiUrl;
	}

	/**
	 * Sets the L2P API server. Returns self for fluent setters
	 * @param string $apiUrl
	 * @return Config
	 */
	public function setApiUrl($apiUrl) {
		if(substr($apiUrl, -1) !== '/'){
			$apiUrl .= '/';
		}
		$this->apiUrl = $apiUrl;
		return $this;
	}
}
