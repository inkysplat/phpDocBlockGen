<?php

error_reporting(E_ALL ^ E_DEPRECATED);

require_once('library/Scan.class.php');
require_once('library/Config.class.php');
@require_once('DocBlockGenerator.php');

$config = new Config();

if (!class_exists('PHP_DocBlockGenerator'))
{
    if (empty($config->app['docBlockGeneratorPath']))
    {
	throw new exception(__FILE__ . "::Missing DocBlock Class Path");
    }
    set_include_path(get_include_path() . PATH_SEPARATOR . $config->app['docBlockGeneratorPath']);
    require_once('DocBlockGenerator.php');
}

$docGen = new PHP_DocBlockGenerator();

if (empty($config->app['inputDirectory']))
{
    throw new exception(__FILE__ . "::Missing Source Directory");
}

$scanFiles = new Scan($config->app['inputDirectory']);
$scanFiles->scan();

if (is_array($config->filelist)
	&& count($config->filelist) >= 1)
{
    $useFileList = true;
}

$thisFilePath = false;

while ($file = $scanFiles->next())
{

    if ($useFileList)
    {
	if (in_array(basename($file), $config->filelist['file']))
	{
	    $thisFilePath = $file;
	}
    } else
    {
	if (substr($file, -4) == '.php')
	{
	    $thisFilePath = $file;
	}
    }

    if (strlen($thisFilePath) > 0)
    {
	if (substr($file, -4) == '.php')
	{
	    if (file_exists($file))
	    {
		try
		{
		    if ($docGen->generate($file, $config->docblock, $file))
		    {
			print("\n" . __LINE__ . "::" . basename($file) . "::Successfully DocBlocked");
		    }
		} catch (exception $e)
		{
		    print("\n" . __FILE__ . "::DocBlock Generator Exception " . $e->getMessage());
		}
	    } else
	    {
		print("\n" . __LINE__ . "::No Such File");
	    }
	}
    }

    $thisFilePath = false;
}
?>
