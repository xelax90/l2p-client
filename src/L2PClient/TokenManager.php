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
use L2PClient\Token\DeviceToken;
use L2PClient\Token\AccessToken;
use L2PClient\Token\RefreshToken;

use DateTime;

/**
 * Manages Access-/Refresh- Token retrieval
 *
 * @author schurix
 */
class TokenManager {
	
	protected $config;
	
	public function __construct(Config $config) {
		$this->config = $config;
	}
	
	/**
	 * @return Config
	 */
	public function getConfig() {
		return $this->config;
	}
	
	/**
	 * Tries to retrieve RefreshToken. 
	 * @return RefreshToken|null
	 */
	protected function getRefreshToken(){
		// check if RefreshToken is already stored
		$refreshToken = $this->getConfig()->getStorage()->getRefreshToken();
		if(null !== $refreshToken){
			return $refreshToken;
		}
		
		// get device token required for RefreshToken request
		$deviceToken = $this->getDeviceToken();
		if($deviceToken !== null && $deviceToken->canPoll()){
			// check if token exists and can be polled (depends on interval provided by the server)
			$params = array(
				'client_id' => $this->getConfig()->getClientId(),
				'code' => $deviceToken->getDeviceCode(),
				'grant_type' => 'device',
			);
			// request a RefreshToken
			$response = $this->sendPostRequest("oauth2waitress/oauth2.svc/token", $params);
			$output = $response['output'];
			$httpcode = $response['code'];
			
			// update LastPoll date to assure the correct polling interval
			$deviceToken->setLastPoll(new DateTime());
			$this->getConfig()->getStorage()->saveDeviceToken($deviceToken);
			
			switch($httpcode){
				case "200" : 
					$responseObject = json_decode($output);
					if(empty($responseObject->access_token)){
						// no AccessToken provided yet. Authorization is most likely pending.
						return null;
					}
					// AccessToken provided
					// create AccessToken and save it
					$accessToken = new AccessToken(new DateTime(), $responseObject->access_token, $responseObject->token_type, $responseObject->expires_in);
					$this->getConfig()->getStorage()->deleteAccessToken();
					$this->getConfig()->getStorage()->saveAccessToken($accessToken);
					if($responseObject->refresh_token){
						// if a RefreshToken was also provided, we create and save it, too.
						$refreshToken = new RefreshToken(new DateTime(), $responseObject->refresh_token);
						$this->getConfig()->getStorage()->saveRefreshToken($refreshToken);
					}
					return $refreshToken;
			}
		}
		return null;
	}
	
	/**
	 * Retrieves AccessToken
	 * Returns null if no AccessToken could be retrieved (e.g. if you need further polling or the authentication is offline)
	 * @return AccessToken|null
	 */
	public function getAccessToken(){
		// get stored AccessToken
		$accessToken = $this->getConfig()->getStorage()->getAccessToken();
		if($accessToken !== null && $accessToken->isExpired()){
			// delete AccessToken if expired
			$this->getConfig()->getStorage()->deleteAccessToken();
			$accessToken = null;
		}
		if($accessToken !== null){
			// return AccessToken if stored and not expired
			return $accessToken;
		}
		// no AccessToken stored
		
		// get RefreshToken. May result in valid AccessToken
		$refreshToken = $this->getRefreshToken();
		if(null === $refreshToken){
			// no RefreshToken provided. need to continue polling.
			return null;
		}
		// check if AccessToken was set
		$accessToken = $this->getConfig()->getStorage()->getAccessToken();
		if($accessToken){
			return $accessToken;
		}
		// otherwise fetch new AccessToken with the RefreshToken
		return $this->getAccessTokenWithRefreshToken($refreshToken);
	}
	
	/**
	 * Get new AccessToken using provided RefreshToken
	 * 
	 * @param RefreshToken $refreshToken
	 * @return AccessToken
	 */
	protected function getAccessTokenWithRefreshToken(RefreshToken $refreshToken){
		// Send post request to authentication service
		$params = array(
			'client_id' => $this->getConfig()->getClientId(),
			'refresh_token' => $refreshToken->getRefreshToken(),
			'grant_type' => 'refresh_token',
		);
		$response = $this->sendPostRequest("oauth2waitress/oauth2.svc/token", $params);
		$output = $response['output'];
		$httpcode = $response['code'];
		
		switch($httpcode){
			case "200" : 
				$responseObject = json_decode($output);
				if(isset($responseObject->error)){
					// an error occured, the RefreshToken is most likely invalid
					// delete RefreshToken to retrieve new one on next request
					$this->getConfig()->getStorage()->deleteRefreshToken();
					return null;
				}
				// create access token and save it
				$accessToken = new AccessToken(new DateTime(), $responseObject->access_token, $responseObject->token_type, $responseObject->expires_in);
				$this->getConfig()->getStorage()->saveAccessToken($accessToken);
				return $accessToken;
		}
		return null;
	}
	
	/**
	 * Retrieves DeviceToken contianing the UserCode and DeviceCode
	 * @return DeviceToken
	 */
	protected function getDeviceToken(){
		// check if valid device token exists
		$deviceToken = $this->getConfig()->getStorage()->getDeviceToken();
		if(null !== $deviceToken && $deviceToken->isExpired()){
			$this->getConfig()->getStorage()->deleteDeviceToken();
			$deviceToken = null;
		}
		
		if(null === $deviceToken){
			// if no token exists, call the code endpoint
			$params = array(
				'client_id' => $this->getConfig()->getClientId(),
				'scope' => $this->getConfig()->getScope(),
			);
			$response = $this->sendPostRequest("oauth2waitress/oauth2.svc/code", $params);
			$output = $response['output'];
			$httpcode = $response['code'];
			switch($httpcode){
				case "200" : 
					// save the recieved DeviceToken
					$responseObject = json_decode($output);
					$deviceToken = new DeviceToken(new DateTime(), $responseObject->device_code, $responseObject->expires_in, $responseObject->interval, $responseObject->user_code, $responseObject->verification_url);
					$this->getConfig()->getStorage()->saveDeviceToken($deviceToken);
			}
		}
		return $deviceToken;
	}
	
	/**
	 * Sends a POST request to the auth server. Returns array with keys 'output' and 'code'. Output contains the response body, code contains the response code.
 	 * @param string $path The endpoint that you want to access
	 * @param string $params The parameters you want to send
	 * @return array
	 */
	protected function sendPostRequest($path, $params){
		$url = $this->getConfig()->getAuthUrl().$path;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT,10);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		$output = curl_exec($ch);

		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		return array('output' => $output, 'code' => $httpcode);
	}
}
