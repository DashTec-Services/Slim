<?php
namespace SP\Views;

use Slim\View;

class MyUltimateView extends View
{
    protected $cssFiles = [];
    protected $jsFiles = [];

    public function fetch($template, $data = null)
    {
        $data = is_array($data) ? $data : [];
        $data['css_files'] = $this->cssFiles;
        $data['js_files'] = $this->jsFiles;

        return parent::fetch($template, $data);
    }

    public function getJSFiles(){
        return $this->jsFiles;
    }

    public function clearCssFiles()
    {
        $this->cssFiles = [];
    }

    public function clearJsFiles()
    {
        $this->jsFiles = [];
    }

    public function addCssFile($cssFile)
    {
        $this->cssFiles[] = $cssFile;
    }

    public function addJsFiles($jsFile)
    {
        $this->jsFiles[] = $jsFile;
    }

    
}