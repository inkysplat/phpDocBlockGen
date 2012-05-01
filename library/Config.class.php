<?php

class Config
{

    private $config = array();

    public function __construct()
    {

	if (!file_exists('config.ini'))
	{
	    throw new exception(__METHOD__ . "::missing configuration file");
	}

	if ($config = parse_ini_file('config.ini', true))
	{
	    foreach ($config as $k => $c)
	    {
		if (is_array($c))
		{
		    $this->config[$k] = (array) $c;
		}

		if (is_string($c))
		{
		    $this->config[$k] = (string) $c;
		}

		if (is_numeric($c))
		{
		    $this->config[$k] = (int) $c;
		}
	    }
	} else
	{
	    throw new exception(__METHOD__ . "::cannot parse configuration file");
	}
    }

    public function __get($key)
    {
	if (array_key_exists($key, $this->config))
	{
	    return $this->config[$key];
	}
    }

}

?>
