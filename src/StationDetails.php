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

namespace aledjones\db_rest_php;

/**
 * Class StationDetails
 * Extends structure of Station
 * @package aledjones\db_rest_php
 */
class StationDetails extends Station
{
    /**
     * @var array
     *
     * @var string
     *
     * @var integer
     *
     * @var \aledjones\db_rest_php\DataTypes\coordinates
     *
     * @var \aledjones\db_rest_php\DataTypes\operator
     *
     * @var \aledjones\db_rest_php\DataTypes\address
     *
     * @var integer
     *
     * @var integer
     *
     * @var boolean
     *
     * @var boolean
     *
     * @var boolean
     *
     * @var boolean
     *
     * @var boolean
     *
     * @var boolean
     *
     * @var boolean
     *
     * @var string
     *
     * @var string
     *
     * @var boolean
     *
     * @var boolean
     *
     * @var boolean
     *
     * @var boolean
     *
     * @var boolean
     *
     * @var boolean
     *
     * @var string
     *
     * @var \aledjones\db_rest_php\DataTypes\regionalbereich
     *
     * @var \aledjones\db_rest_php\DataTypes\availability\DBinformation
     *
     * @var \aledjones\db_rest_php\DataTypes\availability\localServiceStaff
     *
     * @var \aledjones\db_rest_php\DataTypes\timeTableOffice
     *
     * @var \aledjones\db_rest_php\DataTypes\szentrale
     *
     * @var \aledjones\db_rest_php\DataTypes\stationManagement
     *
     * @var array
     */
    public $additionalIds,
        $ds100,
        $nr,
        $coordinates,
        $operator,
        $address,
        $category,
        $priceCategory,
        $hasParking = 0,
        $hasBicycleParking = 0,
        $hasLocalPublicTransport = 0,
        $hasPublicFacilities = 0,
        $hasLockerSystem = 0,
        $hasTaxiRank = 0,
        $hasTravelNecessities = 0,
        $hasSteplessAccess = "no",
        $hasMobilityService,
        $hasWiFi = 0,
        $hasTravelCenter = 0,
        $hasRailwayMission = 0,
        $hasDBLounge = 0,
        $hasLostAndFound = 0,
        $hasCarRental = 0,
        $federalState,
        $regionalbereich,
        $DBinformation,
        $localServiceStaff,
        $timeTableOffice,
        $szentrale,
        $stationManagement,
        $ril100Identifiers;

    function __construct($type,
                         $id,
                         $name,
                         $weight = null,
                         $relevance = null,
                         $score = null)
    {
        parent::__construct($type, $id, $name, $weight, $relevance, $score);
    }
}