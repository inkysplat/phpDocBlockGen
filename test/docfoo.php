<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  TestApplication 
 * @package   BrieApplication 
 * @author    Adam Nicholls <adamnicholls1987@gmail.com>
 * @copyright 2012 Adam Nicholls
 * @license   
 * @version   CVS: $Id:$ 
 * @link      http://pear.php.net/package/BrieApplication 
 * @see       References to other sections (if any)...
 */

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Application
 *
 * @author adam 
 */
abstract class BrieApplication {

    /**
     * Description for protected
     * @var object    
     * @access protected 
     */
    protected $_session;

    /**
     * Description for protected
     * @var object    
     * @access protected 
     */
    protected $_request;

    /**
     * Description for protected
     * @var object    
     * @access protected 
     */
    protected $_view;

    /**
     * Description for protected
     * @var unknown   
     * @access protected 
     */
	protected $_db;

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param unknown $value Parameter description (if any) ...
     * @param unknown $key   Parameter description (if any) ...
     * @return void    
     * @access public  
     */
    public function set($value, $key) {
        $this->{$key} = $value;
    }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param string        $action     Parameter description (if any) ...
     * @param string        $controller Parameter description (if any) ...
     * @param string        $module     Parameter description (if any) ...
     * @return void          
     * @access protected     
     * @throws BrieException Exception description (if any) ...
     * @throws BrieException Exception description (if any) ...
     */
    protected function doRedirect($action, $controller='', $module='') {
        $location = '/';

        if ($action == '')
            throw new BrieException(__METHOD__ . " Missing Action");

        if ($controller != '') {
            if (!class_exists($controller)) {
                throw new BrieException(__METHOD__ . " No Such Controller");
            }
        } else {
            $controller = 'index';
        }

        $location .= $module . $controller . '/' . $action;
        header('location: ' . $location);
        exit();
    }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param unknown   $method Parameter description (if any) ...
     * @param unknown   $params Parameter description (if any) ...
     * @return void      
     * @access protected 
     */
    protected function jump($method, $params) {
        if (method_exists($method)) {
            call_user_func_array(array($this, $method), $params);
        }
    }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void   
     * @access public 
     */
    public function init() {
    	
		if(defined('BrieSession')){
        	$this->_session = BrieSession::getInstance();
		}
		
		if(defined('BrieMySQL')){
			$db = BrieMySQL::getInstance();
			$this->_db = $db->connect();
		}
		
        $this->_request = new BrieRequest();
        $this->_view = new BrieView();
		
		
    }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void   
     * @access public 
     */
    public function end() {
        unset($this->_request);

        $this->_session->commit();
        unset($this->_session);

        $this->_view->render();
        unset($this->_view);
    }

}

