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
 * Original author...
 *
 * Copyright (c) Jordi Boggiano
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Normalizes incoming records to remove objects/resources so it's easier to dump to various targets
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
abstract class ehough_epilog_impl_formatter_AbstractNormalizingFormatter implements ehough_epilog_api_IFormatter
{
    private $_dateFormat;

    /**
     * @param string $dateFormat The format of the timestamp: one supported by DateTime::format
     */
    public function __construct($dateFormat = null)
    {
        $this->_dateFormat = $dateFormat ? : 'Y-m-d H:i:s';
    }

    /**
     * {@inheritdoc}
     */
    public final function format(array $record)
    {
        $returnValue = $this->_normalize($record);

        return $this->_onAfterFormat($record, $returnValue);
    }

    protected final function _normalize($data)
    {
        if (is_float($data)) {

            $val = date($this->_dateFormat, intval($data));

            return $val;
        }

        if (null === $data || is_scalar($data)) {

            return $data;
        }

        if (is_array($data) || $data instanceof Traversable) {

            $normalized = array();

            foreach ($data as $key => $value) {

                $normalized[$key] = $this->_normalize($value);
            }

            return $normalized;
        }

        if (is_object($data)) {

            return sprintf("[object] (%s: %s)", get_class($data), var_export($data, true));
        }

        if (is_resource($data)) {

            return '[resource]';
        }

        return '[unknown(' . gettype($data) . ')]';
    }

    protected function _onAfterFormat(/** @noinspection PhpUnusedParameterInspection */ array $record, $returnValue)
    {
        //override point
        return $returnValue;
    }
}