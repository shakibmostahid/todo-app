<?php

namespace Core;

use Exception;
use Core\Database;

class Controller
{
    public $database;

    /**
     * loads necessary classes
     */
    public function __construct()
    {
        $this->database =  new Database();
    }

    /**
     * Render a view file
     * 
     * @param string $viewPath is view path location
     * @param array $data
     * 
     * @throws Exception on error occurred
     */
    public function render($viewPath = '', array $data = [])
    {
        $viewFullPath = __DIR__ . '/../views/' . $viewPath . '.php';
        try {
            if (!file_exists($viewFullPath)) {
                throw new Exception("View doesn't exist");
            }
            foreach ($data as $key => $value) {
                $$key = $value;
            }
            unset($data);
            require_once($viewFullPath);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Render data in JSON data pattern
     * 
     * @param int $httpCode in HTTP code
     * @param array $data
     * 
     * @throws Exception on invalid data pattern
     */
    public function jsonRender($httpCode, array $data = [])
    {
        if ($data && !is_array($data)) {
            $httpCode = 500;
            $data = ['error' => 'Invalid Data Pattern found in jsonRender()'];
        }
        header("Content-Type: application/json");
        http_response_code($httpCode);
        $data = json_encode($data);
        echo $data;

        exit();
    }

    /**
     * Parse request body for get request data for PUT and DELETE Methods
     * 
     * @return array $result
     */
    public function getRequestBody()
    {
        $data = file_get_contents("php://input");
        parse_str($data, $result);

        return $result;
    }
}
