<?php
/**
 * This file is part of MeCms.
 *
 * MeCms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeCms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeCms.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeCms\Utility;

/**
 * An utility to get information about YouTube videos
 */
class Youtube {
	/**
	 * Parses a YouTube url and returns the YouTube ID
	 * @param string $url Video url
	 * @return mixed Youtube ID or FALSE
	 */
	public static function getId($url) {
		if(preg_match('/youtube\.com/', $url)) {
			$url = parse_url($url);
			
			if(empty($url['query']))
				return FALSE;
			
			parse_str($url['query'], $url);
				
			return empty($url['v']) ? FALSE : $url['v'];
		}
		elseif(preg_match('/youtu.be\/(.+)$/', $url, $matches))
			return empty($matches[1]) ? FALSE : $matches[1];
		else
			return FALSE;
	}
	
	/**
	 * Gets the preview for a video
	 * @param string $id YouTube ID
	 * @return string Url
	 */
	public static function getPreview($id) {
		return sprintf('http://img.youtube.com/vi/%s/0.jpg', $id);
	}
	
	/**
	 * Gets the url for a video
	 * @param string $id YouTube ID
	 * @return string Url
	 */
	public static function getUrl($id) {
		return sprintf('http://youtu.be/%s', $id);
	}
}