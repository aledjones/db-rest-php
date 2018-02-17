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
 * Class Client
 * Provides a simple interface for collecting data structured as Station objects
 * @package aledjones\db_rest_php
 */
class Client
{
    public $base_url;

    function __construct($base_url = "https://1.db.transport.rest/")
    {
        $this->base_url = $base_url;
    }

    /**
     * @param $query
     * @return array of Station objects
     * @throws StationQueryEmptyException
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function GetStationsByQuery($query)
    {
        $s = $this->base_url . 'stations?query=';
        if (!empty($query)) {
            $s .= urlencode($query);

            $c = \Httpful\Request::get($s)
                ->expects('application/json')
                ->send();
            $response = array();

            foreach ($c->body as $item) {
                array_push($response,
                    new Station($item->type,
                        $item->id,
                        $item->name,
                        $item->weight,
                        $item->relevance,
                        $item->score));
            }
            return $response;
        } else {
            throw new StationQueryEmptyException('$query cannot be empty!');
        }
    }

    /**
     * @param $name string
     * $name needs to be the exact name of the station, preferably from db-stations-autocomplete, as the REST-endpoint
     * sends back an invalid json if multiple stations are applicable. (02/18)
     * @return StationDetails
     * @throws StationQueryEmptyException
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function GetStationsByName($name)
    {
        $s = $this->base_url . 'stations?name=';
        if (!empty($name)) {
            $s .= urlencode($name);

            $c = \Httpful\Request::get($s)
                ->expects('application/json')
                ->send();
            $item = $c->body;

            $response = new StationDetails($item->type, $item->id, $item->name, $item->weight);
            foreach ($item as $key => $value) {
                $response->{$key} = $value;
            }

            return $response;

        } else {
            throw new StationQueryEmptyException('$name cannot be empty!');
        }
    }

    /**
     * @param $id
     * @return StationDetails
     * @throws StationIdEmptyException
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function GetStationDetailsByID($id)
    {
        $s = $this->base_url . 'stations/';
        if (!empty($id)) {
            $s .= $id;

            $c = \Httpful\Request::get($s)
                ->expects('application/json')
                ->send();
            $item = $c->body;

            $response = new StationDetails($item->type, $item->id, $item->name, $item->weight);
            foreach ($item as $key => $value) {
                $response->{$key} = $value;
            }
            return $response;
        } else {
            throw new StationIdEmptyException('$id cannot be empty!');
        }
    }
}