<?php
class Template
{
    private $vars = array();
    private $html = '';
    
    function __construct($htmlfile)
    {
       if(!$this->html = file_get_contents($htmlfile))
            throw new Exception("Failed to load template " . $htmlfile);
    }
    
    function addStringVar($key, $str)
    {
       $this->vars[$key] = $str;
    }
    
    function addHtmlVar($key, $str)
    {
        $this->vars[$key] = htmlentities($str);
    }

    function removeVar($key)
    {
        if(isset($this->vars[$key]))
            unset($this->vars[$key]);
    }

    function render()
    {
        $output = $this->html;
        foreach($this->vars as $key => $val)
            $output = str_replace("%$key%", utf8_decode($val), $output);
    
        $output = preg_replace('#%\w+%#', '', $output);
            
        return $output;
    }

}
