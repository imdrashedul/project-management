<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApiCollection extends ResourceCollection
{
    private $_success = [];
    private $_errors = [];
    private $_data = [];
    private $_statusCode = 200;

    public function __construct(private string $message = "", $resource = [])
    {
        parent::__construct($resource);
    }


    public function statusCode(int $code)
    {
        $this->_statusCode = $code;
        return $this;
    }


    /**
     * Add and message to the collection
     * @param string $message
     */
    public function message(string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Add an error to the collection.
     *
     * @param array $error
     * @return $this
     */
    public function errors(array $error)
    {
        $this->_errors = array_merge($this->_errors, $error);
        return $this;
    }

    /**
     * Add success data to the collection.
     *
     * @param array $success
     * @return $this
     */
    public function success(array $success)
    {
        $this->_success = array_merge($this->_success, $success);
        return $this;
    }

    /**
     * Add additional data to the collection.
     *
     * @param array $data
     * @return $this
     */
    public function data(array $data)
    {
        $this->_data = array_merge($this->_data, $data);
        return $this;
    }


    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = [
            'message' => $this->message,
        ];

        if (!empty($this->_errors)) {
            $response['errors'] = $this->_errors;
        }

        if (!empty($this->_success)) {
            $response['success'] = $this->_success;
        }

        if (!empty($this->_data)) {
            $response['data'] = $this->_data;
        }

        return $response;
    }

    public function withResponse($request, $response)
    {
        $response->setData($this->toArray($request));
        $response->setStatusCode($this->_statusCode);
    }
}
