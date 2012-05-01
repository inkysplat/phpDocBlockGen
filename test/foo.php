<?php

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

    protected $_session;
    protected $_request;
    protected $_view;
	protected $_db;

    public function set($value, $key) {
        $this->{$key} = $value;
    }

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

    protected function jump($method, $params) {
        if (method_exists($method)) {
            call_user_func_array(array($this, $method), $params);
        }
    }

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

    public function end() {
        unset($this->_request);

        $this->_session->commit();
        unset($this->_session);

        $this->_view->render();
        unset($this->_view);
    }

}

