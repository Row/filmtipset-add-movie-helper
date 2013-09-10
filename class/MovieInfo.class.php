<?php
/**
*
*/
class MovieInfo
{
    private $imdbId            = '';
    private $title             = '';
    private $originalTitle     = '';
    private $year              = '';
    private $directors         = array();
    private $writers           = array();
    private $actors            = array();
    private $countries         = array();
    private $altTitles         = array();
    private $plot              = '';
    private $runtime           = '';

    function __construct()
    {
        # code...
    }

    public function __toString()
    {
        return $this->title;
    }

    public function setImdbId($var)
    {
        $this->imdbId = $var;
    }

    public function getImdbId()
    {
        return $this->imdbId;
    }

    public function setTitle($var)
    {
        $this->title = $var;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setOriginalTitle($var)
    {
        $this->originalTitle = $var;
    }

    public function getOriginalTitle()
    {
        return $this->originalTitle;
    }

    public function setYear($var)
    {
        $this->year = $var;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function setPlot($var)
    {
        $this->plot = $var;
    }

    public function getPlot()
    {
        return $this->plot;
    }

    public function setRuntime($var)
    {
        $this->runtime = $var;
    }

    public function getRuntime()
    {
        return $this->runtime;
    }

    public function addDirector($string)
    {
        $this->directors[] = (string) $string;
    }

    public function getDirectors()
    {
        return $this->directors;
    }

    public function addWriter($string)
    {
        $this->writers[] = (string) $string;
    }

    public function getWriters()
    {
        return $this->writers;
    }

    public function addActor($string)
    {
        $this->actors[] = (string) $string;
    }

    public function getActors()
    {
        return $this->actors;
    }

    public function addCountry($string)
    {
        $this->countries[] = (string) $string;
    }

    public function removeCountry($string)
    {
        $this->countries[] = array_diff($this->countries, array((string) $string));
    }

    public function getCountries()
    {
        return $this->countries;
    }

    public function addAltTitle($string)
    {
        $this->altTitles[] = (string) $string;
    }

    public function getAltTitles()
    {
        return $this->altTitles;
    }

}
