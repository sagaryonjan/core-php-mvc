<?php

namespace System;

class File
{
    /**
     * Directory Seperator
     *
     * @const string
     */
    const DS = DIRECTORY_SEPARATOR;

    /**
     * Php Extension
     *
     * @const string
     */
    const PHP = '.php';

    /**
     * Root Path
     * @var string
     */
    private $root;

    /**\
     * Constructor
     *
     * @param string $root
     */
    public function __construct($root)
    {
        $this->root = $root;
    }

    /**
     * Determine Whether the given file path exists
     *
     * @param string $file
     * @return bool
     */
    public function exists($file)
    {
        return file_exists($this->to($file));

    }

    /**
     * Require the given file
     *
     * @param string $file
     * @param array $data
     * @return void
     */
    public function call($file, $data = [])
    {
        if (is_array( $data) && !empty($data)){
            extract($data);
        }

        require $this->to($file);
    }

    /**
     * Generate full path to the given path in vendor folder
     *
     * @param string $path
     * @return string
     */
    public function toVendor($path)
    {
        return $this->to('vendor/'.$path);
    }

    /**
     * Generate full path to given path
     *
     * @param string $path
     * @return string
     */
    public function to($path)
    {
        return $this->root . static::DS . str_replace(['/', '\\'], static::DS, $path);
    }

}