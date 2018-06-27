<?php

use Slim\Http\Request;
use Slim\Http\Response;

$dotenv = new Dotenv\Dotenv('../');
$dotenv->load();

$serverUrl = getenv('WEB_SERVICE_URL');


class Employee
{
    /**
     * Get employees.
     *
     * @param int $max
     * @param int $min
     * @return array $greetings
     */

    public function getEmployees($min,$max){

        $employees = json_decode(file_get_contents("./data/employees.json"), true);

        $employees = array_filter($employees, function($employee) use ($min, $max){
            $salary = preg_replace("/([^0-9\\.])/i", "", $employee['salary']);
            return ($salary >= $min) && ($salary <= $max);
        });

        return $employees;
    }

}


// Routes

$app->get('/prueba-service', function (Request $request, Response $response, array $args) use ($serverUrl){

    $client = new \Zend\Soap\Client($serverUrl.'?wsdl');

    $result = $client->getEmployees(['min' => 1000,'max'=>1500]);

    var_dump($result->getEmployeesResult) ;
    die();

});


$app->get('/', function (Request $request, Response $response) {
// Sample log message
    $employees = json_decode(file_get_contents("../data/employees.json"));

    $email = $request->getParam('email');

    if(isset($email) && !empty($email))
    {
        $employees = array_filter($employees, function($employee) use ($email) {
            return $employee->email == $email;
        });
    }

// Render index view
    return $this->renderer->render($response, 'index.phtml', compact('employees'));
});

$app->get('/show/[{id}]', function (Request $request, Response $response, array $args) {
// Sample log message
    $employees = json_decode(file_get_contents("../data/employees.json"));
    $results = array_filter($employees, function($employee) use ($args) {
        return $employee->id == $args['id'];
    });

   $results = array_values($results);
    $args['employee'] = $results[0];

// Render index view
    return $this->renderer->render($response, 'show.phtml', $args);
});




