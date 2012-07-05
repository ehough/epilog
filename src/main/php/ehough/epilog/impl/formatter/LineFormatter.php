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
 * Formats incoming records into a one-line string
 *
 * This is especially useful for logging to files
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Christophe Coevoet <stof@notk.org>
 */
final class ehough_epilog_impl_formatter_LineFormatter extends ehough_epilog_impl_formatter_AbstractNormalizingFormatter
{
    private $_format;

    /**
     * Constructor.
     *
     * @param string $format     The format of the message.
     * @param string $dateFormat The format of the timestamp: one supported by date().
     */
    public function __construct($format = null, $dateFormat = null)
    {
        $this->_format = $format ? : "[%time%] %channel%.%level_name%: %message% %context% %extra%\n";

        parent::__construct($dateFormat);
    }

    /**
     * Formats a set of log records.
     *
     * @param array $records A set of records to format.
     *
     * @return mixed The formatted set of records
     */
    public function formatBatch(array $records)
    {
        $message = '';

        foreach ($records as $record) {

            $message .= $this->format($record);
        }

        return $message;
    }

    /**
     * Override point for normalization.
     *
     * @param array        $data        The original data.
     * @param array|string $returnValue The normalized data.
     *
     * @return mixed The (possibly modified) $returnValue.
     */
    protected function _onAfterFormat(array $data, $returnValue)
    {
        $output = $this->_format;

        foreach ($returnValue['extra'] as $var => $val) {

            if (strpos($output, '%extra.' . $var . '%') === false) {

                continue;
            }

            $output = str_replace('%extra.' . $var . '%', $this->_doConvertToString($val), $output);

            unset($data['extra'][$var]);

        }

        foreach ($data as $var => $val) {

            $output = str_replace('%' . $var . '%', $this->_doConvertToString($val), $output);
        }

        return $output;
    }

    private function _doConvertToString($data)
    {
        $normalized = parent::_normalize($data);

        return stripslashes(parent::_convertToString($normalized));
    }
}