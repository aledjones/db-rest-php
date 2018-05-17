<?php
/**
 * Copyright 2018 Jonas Möller
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace aledjones\db_rest_php\station;


/**
 * Class operator
 * @package aledjones\db_rest_php\station
 */
class operator
{
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    public $type, $id, $name;

    /**
     * operator constructor.
     * @param string $type
     * @param string $id
     * @param string $name
     */
    public function __construct(string $type, string $id, string $name)
    {
        $this->type = $type;
        $this->id = $id;
        $this->name = $name;
    }


}