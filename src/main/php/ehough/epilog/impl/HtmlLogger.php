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
 * along with epilog.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Logger that spits out statements to HTML.
 */
class ehough_epilog_impl_HtmlLogger extends ehough_epilog_impl_SimpleEnableable implements ehough_epilog_api_ISimpleLogger
{
    private $_birthDate;

    /**
     * Print a logging statement.
     *
     * @param string       $prefix    Logging prefix.
     * @param string       $message   Actual logging message.
     * @param unknown_type $arguments An optional list of arguments.
     *
     * @return void
     */
    public function log($prefix, $message, $arguments = null)
    {
        if (! $this->isEnabled()) {

            return;
        }

        /** how many milliseconds have elapsed since birth? */
        $time = number_format((microtime(true) - $this->_birthDate) * 1000, 2);

        if (func_num_args() > 2) {

            $args = func_get_args();

            $message = vsprintf($message, array_slice($args, 2, count($args)));
        }

        /* print it! */
        printf("<div><tt style=\"font-size: small\">%s ms (%s) %s (memory: %s KB)</tt></div>\n", $time, $prefix, $message, number_format(memory_get_usage() / 1024));

    }


    public function onEnable()
    {
        $this->_birthDate = microtime(true);
    }
}