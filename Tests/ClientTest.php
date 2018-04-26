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

use aledjones\db_rest_php;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{

    private $custom_base = 'https://1.db.transport.rest/';

    public function testGetStationsByQuery()
    {
        $client = new db_rest_php\Client($this->custom_base);
        try {
            $results = $client->GetStationsByQuery('Kaiserslautern');
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $this->assertEquals('Kaiserslautern West', $results[1]->name);
        $this->assertEquals('8003150', $results[2]->id);
    }

    public function testGetStationsByName()
    {
        $client = new db_rest_php\Client($this->custom_base);
        try {
            $result = $client->GetStationDetailsByName('Berlin Hbf');
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $this->assertEquals(52.525592, $result->location->latitude);
        $this->assertEquals('Berlin', $result->federalState);
        $this->assertNotEquals('Berlin Hbf', $result->szentrale->name);
        $this->assertArrayHasKey(1, $result->ril100Identifiers);
    }

    public function testGetStationDetailsByID()
    {
        $client = new db_rest_php\Client($this->custom_base);
        try {
            $result = $client->GetStationDetailsById('8002549');
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $this->assertEquals(10.006909, $result->location->longitude);
        $this->assertEquals('Hamburg', $result->federalState);
        $this->assertNotEquals('Berlin Hbf', $result->szentrale->name);
        $this->assertArrayHasKey(1, $result->ril100Identifiers);
        return "Done.";
    }

    /**
     * @expectedException \aledjones\db_rest_php\Exceptions\GenericEndpointErrorException
     * @expectedExceptionMessage Station not found.
     */
    public function testFailGenericEndpointException()
    {
        $client = new db_rest_php\Client($this->custom_base);
        return $client->GetStationDetailsById('999999')->name;
    }

    /**
     * @expectedException aledjones\db_rest_php\Exceptions\StationQueryEmptyException
     */
    public function testFailStationQueryEmptyException()
    {
        $client = new db_rest_php\Client($this->custom_base);
        return $client->GetStationsByQuery("");
    }

    /**
     * @expectedException \aledjones\db_rest_php\Exceptions\StationIdEmptyException
     */
    public function testFailStationIdEmptyException()
    {
        $client = new db_rest_php\Client($this->custom_base);
        return $client->GetStationDetailsById('');
    }
}
