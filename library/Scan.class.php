<?php
/**
 * @name Scan
 * @package Firestorm
 * @subpackage library
 * @category filesystem
 * @author Adam Nicholls <adamnicholls1987@gmail.com>
 * @since 01/05/2012
 * @copyright 2012
 */
class Scan {

    /**
     * Stores directory
     * @access private
     * @var string
     */
    private $directory = FALSE;
    
    /**
     * Stores iterator pointer
     * 
     * @access private
     * @var RecursiveIteratorIterator
     */
    private $files;

    /**
     * Public contructor for Scan class
     * 
     * @access public
     * @param string $directory -- must be valid directory
     * @throws exception 
     */
    public function __construct($directory) {

        if (!is_dir($directory)) {
            throw new exception(__METHOD__ . "::Expected Directory");
        }

        $this->directory = $directory;
    }

    /**
     * Loads the directory tree using the SPL iterator
     * 
     * @access public
     * @return boolean
     * @throws exception 
     */
    public function scan() {

        if (!$this->directory) {
            throw new exception(__METHOD__ . "::No Directory");
        }

        try {
            $files = new RecursiveIteratorIterator(
                            new RecursiveDirectoryIterator($this->directory),
                            RecursiveIteratorIterator::SELF_FIRST
            );
        } catch (exception $e) {
            throw new exception(__METHOD__ . "::" . $e->getMessage());
        }

        if ($files instanceof RecursiveIteratorIterator) {
            $this->files = $files;

            return true;
        }
    }

    /**
     * Return the current file/directory name
     * 
     * @access public
     * @return string
     * @throws exception 
     */
    public function current() {

        if (!($this->files instanceof RecursiveIteratorIterator)) {
            throw new exception(__METHOD__ . "::No Scan Found");
        }

        if (!$this->files->valid()) {
            throw new exception(__METHOD__ . "::Exceeded Result Set");
        }

        $item = $this->files->current();


        return $item->__toString();
    }

    /**
     * Returns and moves the pointer to the next file in tree
     * 
     * @access public
     * @return string
     * @throws exception 
     */
    public function next() {

        if (!($this->files instanceof RecursiveIteratorIterator)) {
            throw new exception(__METHOD__ . "::No Scan Found");
        }

        $this->files->next();

        if (!$this->files->valid()) {
            return false;
        }

        $item = $this->files->current();

        return $item->__toString();
    }

    /**
     * Returns if the current file is a directory
     * 
     * @access public
     * @return boolean
     * @throws exception 
     */
    public function isDir() {

        if (!($this->files instanceof RecursiveIteratorIterator)) {
            throw new exception(__METHOD__ . "::No Scan Found");
        }

        if (!$this->files->valid()) {
            throw new exception(__METHOD__ . "::Exceeded Result Set");
        }

        $item = $this->files->current();

        if ($item->isDir()) {
            return true;
        }

        return false;
    }

    /**
     * Counts all the files in the tree
     * 
     * @access public
     * @return int
     * @throws exception 
     */
    public function count() {
        if (!($this->files instanceof RecursiveIteratorIterator)) {
            throw new exception(__METHOD__ . "::No Scan Found");
        }

        return count($this->files);
    }

}

?>
