<?php
namespace MichaelMartin\Magento\Api\Soap;

class Client extends \SoapClient
{
    /**
     *
     */
    const API_ENDPOINT_V1 = 'api/soap/?wsdl';

    /**
     *
     */
    const API_ENDPOINT_V2 = 'api/v2_soap/?wsdl';
    /**
     *
     */
    const DEFAULT_SOAP_VERSION = 'v2';
    /**
     * @var
     */
    protected $endpoint;

    /**
     * @var
     */
    protected $session;

    /**
     * @var array
     */
    protected $connection;

    /**
     * Constructor.
     *
     * @param array $connection
     * @param array $options
     */
    public function __construct(array $connection, array $options = [])
    {
        $this->connection = $connection;

        parent::__construct($this->getEndpoint(), $options);

        $this->initSession();
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        $url = rtrim($this->connection['site_url'], '/');
        if ($this->getApiVersion() === 'v1') {
            return $url . '/' . self::API_ENDPOINT_V1;
        }
        return $url . '/' . self::API_ENDPOINT_V2;
    }

    /**
     * @return mixed
     */
    public function getApiVersion()
    {
        return array_get($this->connection, 'version', self::DEFAULT_SOAP_VERSION);
    }

    /**
     *
     */
    protected function initSession()
    {
        $this->session = $this->__soapCall('login', $this->getAuthentication());
    }

    /**
     * @return array
     */
    protected function getAuthentication()
    {
        return [$this->connection['user'], $this->connection['key']];
    }

    /**
     *	Get Functions
     *
     *	Extension of the __getFunctions method core to SoapClient
     *
     *	@return array
     */
    public function getFunctions()
    {
        return $this->__getFunctions();
    }

    public function call($name, $arguments = [])
    {
        $arguments = is_array($arguments) ? $arguments : [$arguments];
        $parameters = array_merge([$this->session, $name], [$arguments]);
        return $this->__soapCall('call', $parameters);
    }

    /**
     *	Get Last Response
     *
     *	Extension of the __getLastResponse method core to SoapClient
     *
     *	@return array
     */
    public function getLastResponse()
    {
        return $this->__getLastResponse();
    }

    /**
     * @param string $function_name
     * @param array $arguments
     * @param null $options
     * @param null $input_headers
     * @param null $output_headers
     * @return mixed
     */
    public function __soapCall($function_name, $arguments, $options = null, $input_headers = null, &$output_headers = null)
    {
//        if (!is_null($this->session)) {
//            $arguments = [$this->session, $arguments];
//        }
        return parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);
    }
}