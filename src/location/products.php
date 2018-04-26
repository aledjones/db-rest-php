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

namespace aledjones\db_rest_php\location;


/**
 * Class products
 * provides the products object
 * @package aledjones\db_rest_php\location
 */
class products
{
    /**
     * @var bool
     */
    /**
     * @var bool
     */
    /**
     * @var bool
     */
    /**
     * @var bool
     */
    /**
     * @var bool
     */
    /**
     * @var bool
     */
    /**
     * @var bool
     */
    /**
     * @var bool
     */
    /**
     * @var bool
     */
    /**
     * @var bool
     */
    public $suburban, $subway, $tram, $bus, $ferry, $national, $nationalExp, $regional, $regionalExp, $taxi;

    /**
     * products constructor.
     * @param bool $suburban
     * @param bool $subway
     * @param bool $tram
     * @param bool $bus
     * @param bool $ferry
     * @param bool $national
     * @param bool $nationalExp
     * @param bool $regional
     * @param bool $regionalExp
     * @param bool $taxi
     */
    public function __construct(bool $suburban, bool $subway, bool $tram, bool $bus, bool $ferry, bool $national,
                                bool $nationalExp, bool $regional, bool $regionalExp, bool $taxi)
    {
        $this->suburban = $suburban;
        $this->subway = $subway;
        $this->tram = $tram;
        $this->bus = $bus;
        $this->ferry = $ferry;
        $this->national = $national;
        $this->nationalExp = $nationalExp;
        $this->regional = $regional;
        $this->regionalExp = $regionalExp;
        $this->taxi = $taxi;
    }


}