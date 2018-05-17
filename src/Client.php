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


use aledjones\db_rest_php\location\geo_location;
use aledjones\db_rest_php\location\line;
use aledjones\db_rest_php\location\location;
use aledjones\db_rest_php\location\products;

class Client
{
    private $baseURI, $c;

    /**
     * Client constructor.
     * @param $baseURI
     */
    public function __construct($baseURI = 'https://1.db.transport.rest/')
    {
        $this->baseURI = $baseURI;
        $this->c = new \GuzzleHttp\Client(['base_uri' => $this->baseURI]);
    }

    public function stations_query(string $query, int $results = 3, bool $completion = true, bool $fuzzy = false)
    {
        if (!empty($query)) {
            $response = $this->c->request('GET', 'stations',
                ['query' => [
                    'query' => $query,
                    'results' => $results,
                    'completion' => $completion,
                    'fuzzy' => $fuzzy
                ]]);
            $body = json_decode($response->getBody());
            if (empty($body)) {
                throw new Exceptions\GenericEndpointEmptyResponseException('Response is empty.');
            }

            $return = array();

            foreach ($body as $item) {
                $r = new \aledjones\db_rest_php\station\station($item->type, $item->id, $item->name, $item->weight);
                foreach ($item as $key => $value) {
                    $r->{$key} = $this->switchKey($key, $value);
                }
                array_push($return, $r);
            }
            return $return;

        } else {
            throw new Exceptions\StationQueryEmptyException('query cannot be empty!');
        }
    }

    public function locations(string $query, int $results = 10, bool $stations = true, bool $poi = true, bool $addresses = true)
    {
        if (!empty($query)) {
            $response = $this->c->request('GET', 'locations',
                ['query' => [
                    'query' => $query,
                    'results' => $results,
                    'stations' => $stations,
                    'poi' => $poi,
                    'addresses' => $addresses
                ]]);
            $item = json_decode($response->getBody());

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

    private function switchKey($key, $value)
    {
        switch ($key) {
            case "location":
                return new DataTypes\location($value->type, $value->latitude, $value->longitude);
                break;

            case "operator":
                return new station\operator($value->type, $value->id, $value->name);
                break;

            case "address":
                return new station\address($value->city, $value->zipcode, $value->street);
                break;

            case "regionalbereich":
                return new station\regionalbereich($value->number, $value->name, $value->shortName);
                break;

            case "DBinformation":
                return new station\availability\DBinformation(
                    new station\availability\availability(
                        new station\availability\monday($value->availability->monday->fromTime,
                            $value->availability->monday->toTime),
                        new station\availability\tuesday($value->availability->tuesday->fromTime,
                            $value->availability->tuesday->toTime),
                        new station\availability\wednesday($value->availability->wednesday->fromTime,
                            $value->availability->wednesday->toTime),
                        new station\availability\thursday($value->availability->thursday->fromTime,
                            $value->availability->thursday->toTime),
                        new station\availability\friday($value->availability->friday->fromTime,
                            $value->availability->friday->toTime),
                        new station\availability\saturday($value->availability->saturday->fromTime,
                            $value->availability->saturday->toTime),
                        new station\availability\sunday($value->availability->sunday->fromTime,
                            $value->availability->sunday->toTime),
                        new station\availability\holiday($value->availability->holiday->fromTime,
                            $value->availability->holiday->toTime)
                    )
                );
                break;

            case "localServiceStaff":
                return new station\availability\localServiceStaff(
                    new station\availability\availability(
                        new station\availability\monday($value->availability->monday->fromTime,
                            $value->availability->monday->toTime),
                        new station\availability\tuesday($value->availability->tuesday->fromTime,
                            $value->availability->tuesday->toTime),
                        new station\availability\wednesday($value->availability->wednesday->fromTime,
                            $value->availability->wednesday->toTime),
                        new station\availability\thursday($value->availability->thursday->fromTime,
                            $value->availability->thursday->toTime),
                        new station\availability\friday($value->availability->friday->fromTime,
                            $value->availability->friday->toTime),
                        new station\availability\saturday($value->availability->saturday->fromTime,
                            $value->availability->saturday->toTime),
                        new station\availability\sunday($value->availability->sunday->fromTime,
                            $value->availability->sunday->toTime),
                        new station\availability\holiday($value->availability->holiday->fromTime,
                            $value->availability->holiday->toTime)
                    )
                );
                break;

            case "timeTableOffice":
                return new station\timeTableOffice($value->email, $value->name);
                break;

            case "szentrale":
                return new station\szentrale($value->number, $value->publicPhoneNumber, $value->name);
                break;

            case "stationManagement":
                return new station\stationManagement($value->number, $value->name);
                break;

            default:
                return $value;
        }
    }
}