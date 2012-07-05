<?php
/**
 * Copyright 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of epilog (https://github.com/ehough/epilog)
 *
 * epilog is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * epilog is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Simple class to hold a Unix timestamp.
 */
final class ehough_epilog_impl_TimeStamp
{
    /** Timestamp. */
    private $_timestamp;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_timestamp = time();
    }

    /**
     * Format this timestamp to human-readable.
     *
     * @param string $dateFormat The date format to use.
     *
     * @return string The formatted time.
     */
    public function format($dateFormat)
    {
        return date($dateFormat, $this->_timestamp);
    }
}