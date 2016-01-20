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

use L2PClient\Token\AccessToken;

/**
 * Basic client for the L2P APi
 *
 * @author schurix
 */
class Client {
	
	/**
	 * API Configuration
	 * @var Config
	 */
	protected $config;
	
	/**
	 * @var TokenManager
	 */
	protected $tokenManager;
	
	public function __construct(Config $config) {
		$this->config = $config;
	}
	
	
	/**
	 * Returns the API Configuration
	 * @return Config
	 */
	public function getConfig() {
		return $this->config;
	}
	
	/**
	 * <
	 * @param Config $config
	 * @return L2P
	 */
	public function setConfig(Config $config) {
		$this->config = $config;
		return $this;
	}
	
	
	/**
	 * Returns the token manager. Creates it if necessary
	 * @return TokenManager
	 */
	public function getTokenManager(){
		if(null === $this->tokenManager){
			$this->tokenManager = new TokenManager($this->getConfig());
		}
		return $this->tokenManager;
	}
	
	/**
	 * Tries to retrieve access token.
	 * @return AccessToken
	 */
	public function getAccessToken(){
		$accessToken = $this->getTokenManager()->getAccessToken();
		return $accessToken;
	}
	
	/**
	 * Request an API endpoint with post or get method and given parameters. Returns null if no AccessToken is provided
	 * @param string $endpoint
	 * @param boolean $isPost
	 * @param array $params
	 * @return array|null
	 */
	public function request($endpoint, $isPost = false, $params = array()){
		$accessToken = $this->getAccessToken();
		if($accessToken === null){
			return null;
		}
		$params['accessToken'] = $accessToken->getAccessToken();
		$url = $this->getConfig()->getApiUrl().$endpoint;
		if(!$isPost){
			$url .= '?'.http_build_query($params);
		}
		$ch = curl_init($url);
		if($isPost){
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT,10);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		$output = curl_exec($ch);

		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		return array('output' => $output, 'code' => $httpcode);
	}
}
