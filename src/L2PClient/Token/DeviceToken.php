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
 * Description of DeviceToken
 *
 * @author schurix
 */
class DeviceToken extends Token{
	
	protected $deviceCode;
	
	protected $expiryTime;
	
	protected $interval;
	
	protected $userCode;
	
	protected $verificationUrl;
	
	protected $lastPoll = null;
	
	public function __construct($issueTime, $deviceCode, $expiryTime, $interval, $userCode, $verificationUrl, $lastPoll = null) {
		parent::__construct($issueTime);
		$this->setDeviceCode($deviceCode);
		$this->setExpiryTime($expiryTime);
		$this->setInterval($interval);
		$this->setUserCode($userCode);
		$this->setVerificationUrl($verificationUrl);
		if($lastPoll !== null){
			$this->setLastPoll($lastPoll);
		}
	}
	
	public function getDeviceCode() {
		return $this->deviceCode;
	}

	public function getExpiryTime() {
		return $this->expiryTime;
	}

	public function getInterval() {
		return $this->interval;
	}

	public function setDeviceCode($deviceCode) {
		$this->deviceCode = $deviceCode;
		return $this;
	}

	public function setExpiryTime($expiryTime) {
		$this->expiryTime = $expiryTime;
		return $this;
	}

	public function setInterval($interval) {
		$this->interval = $interval;
		return $this;
	}

	public function getUserCode() {
		return $this->userCode;
	}

	public function getVerificationUrl() {
		return $this->verificationUrl;
	}

	public function setUserCode($userCode) {
		$this->userCode = $userCode;
		return $this;
	}

	public function setVerificationUrl($verificationUrl) {
		$this->verificationUrl = $verificationUrl;
		return $this;
	}
	
	public function getLastPoll() {
		return $this->lastPoll;
	}

	public function setLastPoll($lastPoll) {
		$this->lastPoll = $lastPoll;
		return $this;
	}

	public function isExpired(){
		$time = clone $this->getIssueTime();
		$time->modify('+'.$this->getExpiryTime().' seconds');
		$now = new DateTime();
		return $now > $time;
	}
	
	public function canPoll(){
		if($this->getLastPoll() === null){
			return true;
		}
		$time = clone $this->getLastPoll();
		$time->modify('+'.$this->getInterval().' seconds');
		$now = new DateTime();
		return $now > $time;
	}
	
	public function jsonSerialize() {
		$data = parent::jsonSerialize();
		return array_merge($data, array(
			'deviceCode' => $this->getDeviceCode(),
			'expiryTime' => $this->getExpiryTime(),
			'interval' => $this->getInterval(),
			'userCode' => $this->getUserCode(),
			'verificationUrl' => $this->getVerificationUrl(),
			'lastPoll' => $this->getLastPoll(),
		));
	}
	
	public function buildVerificationUrl(){
		$url = $this->getVerificationUrl();
		$params = array(
			'q' => 'verify',
			'd' => $this->getUserCode(),
		);
		$url .= '?'.http_build_query($params);
		return $url;
	}
}
