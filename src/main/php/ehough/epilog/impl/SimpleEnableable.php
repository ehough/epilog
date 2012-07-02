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
 * Abstract item that can be enabled.
 */
class ehough_epilog_impl_SimpleEnableable implements ehough_epilog_api_IEnableable
{
    private $_enabled = false;

    /**
     * Conditionally enables the item.
     *
     * @param boolean $enabled Whether or not to enable the item.
     *
     * @return void
     */
    public final function setEnabled($enabled)
    {
        $this->_enabled = $enabled;

        if ($enabled) {

            $this->onEnable();

        } else {

            $this->onDisable();
        }
    }

    /**
     * Determines if the item is currently enabled.
     *
     * @return boolean True if the item is currently enabled, false otherwise.
     */
    public final function isEnabled()
    {
        return $this->_enabled;
    }

    /**
     * Hook for when the item is enabled.
     *
     * @return void
     */
    protected function onEnable()
    {
        //override point
    }

    /**
     * Hook for when the item is disabled.
     *
     * @return void
     */
    protected function onDisable()
    {
        //override point
    }
}