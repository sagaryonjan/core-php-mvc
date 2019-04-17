<?php

namespace System\View;

use System\File;

class View implements ViewInterface
{

    /**
     * File Object
     *
     * @var \System\File
     */
    private $file;

    /**
     * View Path
     *
     * @var string
     */
    private $viewPath;

    /**
     * Passed Data "variables " to view path
     *
     * @var array
     */
    private $data = [];

    /**
     * The output from the view file
     *
     * @var string
     */
    private $output;

    /**
     * Constructor
     *
     * @param \System\File $file
     * @param string $viewPath
     * @param array $data
     */
    public function __construct(File $file, $viewPath, array $data)
    {
        $this->file = $file;

        $this->preparePath($viewPath);

        $this->data = $data;

    }

    /**
     * Prepare View Path
     *
     * @param string $viewPath
     * @return void
     */
    private function preparePath($viewPath)
    {
        $this->viewPath = $this->file->to('App/Views/'.$viewPath. '.php');

        if(! $this->viewFileExists('App/Views/'.$viewPath. '.php'))
            die($viewPath.' does nto exits in view folder');
    }

    /**
     * Determine if the view file exists
     *
     * @param string $viewPath
     * @return bool
     */
    private function viewFileExists($viewPath)
    {
        return $this->file->exists($viewPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutPut()
    {
        if( is_null( $this->output ) )
        {
            ob_start();

            extract($this->data);

             require $this->viewPath;

            $this->output = ob_get_clean();
        }

        return $this->output;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString() : string
    {
        return $this->getOutPut();
    }


}