<?php

require_once __DIR__ . '/vendor/autoload.php';
ini_set("soap.wsdl_cache_enabled", "0");
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

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


$serverUrl = getenv('WEB_SERVICE_URL');
$options = [
    'uri' => $serverUrl,
];
$server = new Zend\Soap\Server(null, $options);

if (isset($_GET['wsdl'])) {
    $soapAutoDiscover = new \Zend\Soap\AutoDiscover(new \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence());
    $soapAutoDiscover->setBindingStyle(array('style' => 'document'));
    $soapAutoDiscover->setOperationBodyStyle(array('use' => 'literal'));
    $soapAutoDiscover->setClass('Employee');
    $soapAutoDiscover->setUri($serverUrl);

    header("Content-Type: text/xml");
    echo $soapAutoDiscover->generate()->toXml();
} else {
    $soap = new \Zend\Soap\Server($serverUrl . '?wsdl');
    $soap->setObject(new \Zend\Soap\Server\DocumentLiteralWrapper(new Employee()));
    $soap->handle();
}