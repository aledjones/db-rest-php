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

use Httpful\Request;

/**
 * Class Client
 * Provides a simple interface for collecting data structured as Station objects
 * @package aledjones\db_rest_php
 */
class Client
{
    /**
     * @var string
     */
    private $base_url;

    /**
     * Client constructor.
     * @param $base_url string
     */
    function __construct($base_url = "https://1.db.transport.rest/")
    {
        $this->base_url = $base_url;
    }

    /**
     * Returns array of Stations which match the given query
     * @param $query string
     * @return array
     * @throws Exceptions\GenericEndpointErrorException
     * @throws Exceptions\StationQueryEmptyException
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function GetStationsByQuery($query)
    {
        $s = $this->base_url . 'stations?query=';
        if (!empty($query)) {
            $s .= urlencode($query);

            $c = Request::get($s)
                ->expects('application/json')
                ->send();
            if (empty($c->body)) {
                throw new Exceptions\GenericEndpointErrorException('No stations found!');
            }

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
            throw new Exceptions\StationQueryEmptyException('$query cannot be empty!');
        }
    }

    /**
     * Returns a StationDetails object containing all information about the given station name.
     * @param $name string
     * $name needs to be the exact name of the station, preferably from db-stations-autocomplete, as the REST-endpoint
     * sends back an invalid json if multiple stations are applicable. (02/18)
     * @return StationDetails
     * @throws \aledjones\db_rest_php\Exceptions\GenericEndpointErrorException
     * @throws \aledjones\db_rest_php\Exceptions\StationQueryEmptyException
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function GetStationDetailsByName($name)
    {
        $s = $this->base_url . 'stations?name=';
        if (!empty($name)) {
            $s .= urlencode($name);

            $c = Request::get($s)
                ->expects('application/json')
                ->send();
            $item = $c->body;
            if (isset($item->error)) {
                throw new Exceptions\GenericEndpointErrorException($item->msg);
            }

            $response = new StationDetails($item->type, $item->id, $item->name, $item->weight);
            foreach ($item as $key => $value) {
                $response->{$key} = $this->switchKey($key, $value);
            }

            return $response;

        } else {
            throw new \aledjones\db_rest_php\Exceptions\StationQueryEmptyException('$name cannot be empty!');
        }
    }

    /**
     * Determines if a custom data type is applicable and sets it correctly.
     * Returns the given key/value pair by default.
     * @param $key string
     * @param $value mixed
     * @return DataTypes\address|DataTypes\coordinates|DataTypes\operator|DataTypes\regionalbereich|DataTypes\stationManagement|DataTypes\szentrale|DataTypes\timeTableOffice|DataTypes\availability\DBinformation|DataTypes\availability\localServiceStaff
     */
    private function switchKey($key, $value)
    {
        switch ($key) {
            case "coordinates":
                return new DataTypes\coordinates($value->latitude, $value->longitude);
                break;

            case "operator":
                return new DataTypes\operator($value->type, $value->id, $value->name);
                break;

            case "address":
                return new DataTypes\address($value->city, $value->zipcode, $value->street);
                break;

            case "regionalbereich":
                return new DataTypes\regionalbereich($value->number, $value->name, $value->shortName);
                break;

            case "DBinformation":
                return new DataTypes\availability\DBinformation(
                    new DataTypes\availability\availability(
                        new DataTypes\availability\monday($value->availability->monday->fromTime,
                            $value->availability->monday->toTime),
                        new DataTypes\availability\tuesday($value->availability->tuesday->fromTime,
                            $value->availability->tuesday->toTime),
                        new DataTypes\availability\wednesday($value->availability->wednesday->fromTime,
                            $value->availability->wednesday->toTime),
                        new DataTypes\availability\thursday($value->availability->thursday->fromTime,
                            $value->availability->thursday->toTime),
                        new DataTypes\availability\friday($value->availability->friday->fromTime,
                            $value->availability->friday->toTime),
                        new DataTypes\availability\saturday($value->availability->saturday->fromTime,
                            $value->availability->saturday->toTime),
                        new DataTypes\availability\sunday($value->availability->sunday->fromTime,
                            $value->availability->sunday->toTime),
                        new DataTypes\availability\holiday($value->availability->holiday->fromTime,
                            $value->availability->holiday->toTime)
                    )
                );
                break;

            case "localServiceStaff":
                return new DataTypes\availability\localServiceStaff(
                    new DataTypes\availability\availability(
                        new DataTypes\availability\monday($value->availability->monday->fromTime,
                            $value->availability->monday->toTime),
                        new DataTypes\availability\tuesday($value->availability->tuesday->fromTime,
                            $value->availability->tuesday->toTime),
                        new DataTypes\availability\wednesday($value->availability->wednesday->fromTime,
                            $value->availability->wednesday->toTime),
                        new DataTypes\availability\thursday($value->availability->thursday->fromTime,
                            $value->availability->thursday->toTime),
                        new DataTypes\availability\friday($value->availability->friday->fromTime,
                            $value->availability->friday->toTime),
                        new DataTypes\availability\saturday($value->availability->saturday->fromTime,
                            $value->availability->saturday->toTime),
                        new DataTypes\availability\sunday($value->availability->sunday->fromTime,
                            $value->availability->sunday->toTime),
                        new DataTypes\availability\holiday($value->availability->holiday->fromTime,
                            $value->availability->holiday->toTime)
                    )
                );
                break;

            case "timeTableOffice":
                return new DataTypes\timeTableOffice($value->email, $value->name);
                break;

            case "szentrale":
                return new DataTypes\szentrale($value->number, $value->publicPhoneNumber, $value->name);
                break;

            case "stationManagement":
                return new DataTypes\stationManagement($value->number, $value->name);
                break;

            default:
                return $value;
        }
    }

    /**
     * Returns a StationDetails object containing all information about the given station id.
     * @param $id
     * @return StationDetails
     * @throws Exceptions\GenericEndpointErrorException
     * @throws Exceptions\StationIdEmptyException
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function GetStationDetailsById($id)
    {
        $s = $this->base_url . 'stations/';
        if (!empty($id)) {
            $s .= $id;

            $c = Request::get($s)
                ->expects('application/json')
                ->send();
            $item = $c->body;
            if (isset($item->error)) {
                throw new \aledjones\db_rest_php\Exceptions\GenericEndpointErrorException($item->msg);
            }

            $response = new StationDetails($item->type, $item->id, $item->name, $item->weight);
            foreach ($item as $key => $value) {
                $response->{$key} = $this->switchKey($key, $value);
            }
            return $response;
        } else {
            throw new \aledjones\db_rest_php\Exceptions\StationIdEmptyException('$id cannot be empty!');
        }
    }
}