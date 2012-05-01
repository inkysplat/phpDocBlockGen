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

    set_include_path(
	    get_include_path() .
	    PATH_SEPARATOR .
	    $config->app['docBlockGeneratorPath']);

    require_once('DocBlockGenerator.php');
}

$docGen = new PHP_DocBlockGenerator();

if (empty($config->app['inputDirectory']))
{
    throw new exception(__FILE__ . "::Missing Source Directory");
}

$scanFiles = new Scan($config->app['inputDirectory']);
$scanFiles->scan();

$includeFileList = array();
$excludeFileList = array();
$includeDirList = array();
$excludeDirList = array();

if (is_array($config->filelist))
{
    if (!empty($config->filelist['include']))
    {
	$includeFileList = $config->filelist['include'];
    }

    if (!empty($config->filelist['exclude']))
    {
	$excludeFileList = $config->filelist['exclude'];
    }
}

if (is_array($config->dirlist))
{
    if (!empty($config->dirlist['include']))
    {
	$includeDirList = $config->dirlist['include'];
    }

    if (!empty($config->dirlist['exclude']))
    {
	$excludeDirList = $config->dirlist['exclude'];
    }
}

$thisFilePath = false;

while ($file = $scanFiles->next())
{

    if (!empty($includeFileList))
    {
	if (in_array(basename($file), $includeFileList))
	{
	    $thisFilePath = $file;
	}
    } else
    if (!empty($includeDirList))
    {
	foreach ($includeDirList as $includedDir)
	{
	    if (strpos($file, $includedDir))
	    {
		$thisFilePath = $file;
		continue;
	    }
	}
    } else
    {
	if (substr($file, -4) == '.php')
	{
	    $thisFilePath = $file;
	}
    }

    if ($thisFilePath && !empty($excludeFileList))
    {
	if (in_array(basename($file), $excludeFileList))
	{
	    $thisFilePath = false;
	    print("\n" . __LINE__ . "::" . basename($file) . "::Excluded File");
	}
    }

    if ($thisFilePath && !empty($excludeDirList))
    {
	foreach ($excludeDirList as $excludedDir)
	{
	    if (strpos($thisFilePath, $excludedDir))
	    {
		$thisFilePath = false;
		print("\n".__LINE__."::".basename($file)."::Excluded Directory");
		continue;
	    }
	}
    }

    if ($thisFilePath && strlen($thisFilePath) > 0)
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
