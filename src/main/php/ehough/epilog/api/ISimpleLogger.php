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
 * Extremely simple logger with no severity levels.
 */
interface ehough_epilog_api_ISimpleLogger extends ehough_epilog_api_IEnableable
{
    /**
     * Print a logging statement.
     *
     * @param string       $prefix  Logging prefix.
     * @param string       $message Actual logging message.
     * @param unknown_type $args An optional list of arguments.
     *
     * @return void
     */
    function log($prefix, $message, $args = null);
}
