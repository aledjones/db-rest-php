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

use aledjones\db_rest_php\Exceptions\GenericEndpointEmptyResponseException;
use aledjones\db_rest_php\Exceptions\LocationsQueryEmptyException;
use aledjones\db_rest_php\location\geo_location;
use aledjones\db_rest_php\location\line;
use aledjones\db_rest_php\location\location;
use aledjones\db_rest_php\location\products;
use Httpful\Request;

/**
 * Class Client
 * Provides a simple interface for collecting data structured as Station objects
 * @package aledjones\db_rest_php
 */
class ClientOld
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
     * @param bool|null $completion
     * @param bool|null $fuzzy
     * @return array
     * @throws Exceptions\GenericEndpointEmptyResponseException
     * @throws Exceptions\StationQueryEmptyException
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function GetStationsByQuery(string $query, $results = 3, bool $completion = null, bool $fuzzy = null)
    {
        $s = $this->base_url . 'stations?query=';
        if (!empty($query)) {
            $s .= urlencode($query) . "&completion=" . ($completion ? 'true' : 'false') . "&fuzzy=" . ($fuzzy ? 'true' : 'false' . '&results=' . $results);

            $c = Request::get($s)
                ->expects('application/json')
                ->send();
            if (empty($c->body)) {
                throw new Exceptions\GenericEndpointEmptyResponseException('Response is empty.');
            }

            $return = array();

            foreach ($c->body as $item) {
                $r = new StationDetails($item->type, $item->id, $item->name, $item->weight);
                foreach ($item as $key => $value) {
                    $r->{$key} = $this->switchKey($key, $value);
                }
                array_push($return, $r);
            }
            return $return;
        } else {
            throw new Exceptions\StationQueryEmptyException('$query cannot be empty!');
        }
    }

    /**
     * Determines if a custom data type is applicable and sets it correctly.
     * Returns the given key/value pair by default.
     * @param $key string
     * @param $value mixed
     * @return DataTypes\address|DataTypes\location|DataTypes\operator|DataTypes\regionalbereich|DataTypes\stationManagement|DataTypes\szentrale|DataTypes\timeTableOffice|DataTypes\availability\DBinformation|DataTypes\availability\localServiceStaff
     */
    private function switchKey($key, $value)
    {
        switch ($key) {
            case "coordinates":
                return new DataTypes\location($value->type, $value->latitude, $value->longitude);
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
     * Returns a StationDetails object containing all information about the given station name.
     * @param $name string
     * $name needs to be the exact name of the station, preferably from db-stations-autocomplete, as the REST-endpoint
     * sends back an invalid json if multiple stations are applicable. (02/18)
     * @return StationDetails
     * @throws \aledjones\db_rest_php\Exceptions\GenericEndpointErrorException
     * @throws \aledjones\db_rest_php\Exceptions\StationQueryEmptyException
     * @throws \Httpful\Exception\ConnectionErrorException
     * @throws \aledjones\db_rest_php\Exceptions\GenericEndpointEmptyResponseException
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
            } elseif (empty($item)) {
                throw new Exceptions\GenericEndpointEmptyResponseException('Response is empty.');
            }

            $return = new StationDetails($item->type, $item->id, $item->name, $item->weight);
            foreach ($item as $key => $value) {
                $return->{$key} = $this->switchKey($key, $value);
            }

            return $return;

        } else {
            throw new Exceptions\StationQueryEmptyException('$name cannot be empty!');
        }
    }

    /**
     * Returns a StationDetails object containing all information about the given station id.
     * @param $id string | int
     * @return StationDetails
     * @throws Exceptions\GenericEndpointErrorException
     * @throws Exceptions\StationIdEmptyException
     * @throws \Httpful\Exception\ConnectionErrorException
     * @throws GenericEndpointEmptyResponseException
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
                throw new Exceptions\GenericEndpointErrorException($item->msg);
            } elseif (empty($item)) {
                throw new GenericEndpointEmptyResponseException('Response is empty.');
            }

            $return = new StationDetails($item->type, $item->id, $item->name, $item->weight);
            foreach ($item as $key => $value) {
                $return->{$key} = $this->switchKey($key, $value);
            }
            return $return;
        } else {
            throw new Exceptions\StationIdEmptyException('$id cannot be empty.');
        }
    }

    /**
     * @param $id
     * @param string $when
     * @param int $duration
     * @return array
     * @throws Exceptions\GenericEndpointErrorException
     * @throws Exceptions\StationIdEmptyException
     * @throws GenericEndpointEmptyResponseException
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function GetDepartureBoard($id, $when = 'now', $duration = 10)
    {
        $s = $this->base_url . 'stations/';
        if (!empty($id)) {
            $s .= $id . '/departures?when=' . urlencode($when) . '&duration=' . urlencode($duration);

            $c = Request::get($s)
                ->expects('application/json')
                ->send();
            $item = $c->body;
            if (isset($item->error)) {
                throw new Exceptions\GenericEndpointErrorException($item->msg);
            } elseif (empty($item)) {
                throw new GenericEndpointEmptyResponseException('Response is empty.');
            }

            $return = array();
            foreach ($item as $current) {
                if (!is_null($current->when)) {
                    $departure = new Departure($current->journeyId, $current->station,
                        strtotime($current->when),
                        $current->direction, $current->line, $current->remarks, $current->trip, $current->delay);
                    array_push($return, $departure);
                }
            }

            return $return;
        } else {
            throw new Exceptions\StationIdEmptyException('$id cannot be empty.');
        }
    }

    /**
     * @param string $query
     * @param int $results
     * @param bool $stations
     * @param bool $poi
     * @param bool $addresses
     * @return array|void
     * @throws Exceptions\GenericEndpointErrorException
     * @throws GenericEndpointEmptyResponseException
     * @throws LocationsQueryEmptyException
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function locations(string $query, int $results = 10, bool $stations = true, bool $poi = true, bool $addresses = true)
    {
        if (!empty($query)) {
            $c = Request::get($this->base_url . 'locations?query=' . urlencode($query) .
                '&results=' . urlencode($results) . '&stations=' . urlencode($stations) . '&poi=' . urlencode($poi) .
                '&addresses=' . urlencode($addresses))
                ->expects('application/json')
                ->send();

            $item = $c->body;

            if (isset($item->error)) {
                throw new Exceptions\GenericEndpointErrorException($item->msg);
                return;
            } elseif (empty($item)) {
                throw new GenericEndpointEmptyResponseException('Response is empty.');
                return;
            }

            $return = [];
            foreach ($item as $current) {
                $lines = [];
                foreach ($current->lines as $tmp) {
                    try {
                        $line = new line($tmp->type,
                            $tmp->id,
                            $tmp->name,
                            $tmp->public,
                            $tmp->class,
                            $tmp->product,
                            $tmp->mode);
                        array_push($lines, $line);
                    } catch (\Exception $e) {
                    }
                }
                $location = new location($current->type, $current->id, $current->name,
                    new geo_location($current->location->type,
                        $current->location->latitude,
                        $current->location->longitude),
                    new products($current->products->suburban,
                        $current->products->subway,
                        $current->products->tram,
                        $current->products->bus,
                        $current->products->ferry,
                        $current->products->national,
                        $current->products->nationalExp,
                        $current->products->regional,
                        $current->products->regionalExp,
                        $current->products->taxi),
                    $lines);
                array_push($return, $location);
            }

            return $return;

        } else {
            throw new LocationsQueryEmptyException("query cannot be empty");
        }
    }
}