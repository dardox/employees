<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class Employee
{
    protected $client;
    private $username;
    private $password;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->username = env('API_AUTH_USERNAME');
        $this->password = env('API_AUTH_PASSWORD');
    }

    /**
     * Get all employees from the api
     * @return array
     */
    public function all()
    {
        return $this->endpointRequest(env('REWARD_API_URL').'list');
    }


    /**
     * @param $url
     * @return mixed
     */
    public function endpointRequest($url)
    {
        try {
            $response = $this->client->request('GET', $url, ['auth' => [$this->username, $this->password]]);
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'data' => []
            ];
        }

        return $this->responseHandler($response);
    }

    /**
     * @param $response
     * @return array
     */
    public function responseHandler($response)
    {
        if(!empty($response) && $response->getBody() !== 'null') {
            return [
                'status' => 'success',
                'data' => json_decode($response->getBody()->getContents())
            ];
        }

        return [
            'status' => 'error',
            'data' => []
        ];
    }

    /** Pagination
     * @param $request
     * @param $data
     * @return Paginator
     */
    public function paginate($request, $data) {
        $currentPage = Paginator::resolveCurrentPage();
        $col = collect($data);
        $perPage = 20;
        $currentPageItems = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $employees = new Paginator($currentPageItems, count($col), $perPage);
        $employees->setPath($request->url());
        $employees->appends($request->all());
        return $employees;
    }
}