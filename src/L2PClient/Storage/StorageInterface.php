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

use L2PClient\Token\RefreshToken;
use L2PClient\Token\AccessToken;
use L2PClient\Token\DeviceToken;

/**
 * Description of Storage
 *
 * @author schurix
 */
interface StorageInterface {
	
	/**
	 * Stores refresh token
	 * @param RefreshToken $refreshToken
	 */
	public function saveRefreshToken(RefreshToken $refreshToken);
	
	/**
	 * returns stored refresh token or null if nothing stored.
	 * @return RefreshToken
	 */
	public function getRefreshToken();
	
	/**
	 * Deletes stored refresh token
	 */
	public function deleteRefreshToken();
	
	/**
	 * Stores access token
	 * @param AccessToken $accessToken
	 */
	public function saveAccessToken(AccessToken $accessToken);
	
	/**
	 * returns stored access token or null if nothing stored.
	 * @return AccessToken
	 */
	public function getAccessToken();
	
	/**
	 * Deletes stored access token
	 */
	public function deleteAccessToken();
	
	/**
	 * Stores device token
	 * @param DeviceToken $deviceToken
	 */
	public function saveDeviceToken(DeviceToken $deviceToken);
	
	/**
	 * returns stored device token or null if nothing stored.
	 * @return DeviceToken
	 */
	public function getDeviceToken();
	/**
	 * Deletes stored device token
	 */
	public function deleteDeviceToken();
}
