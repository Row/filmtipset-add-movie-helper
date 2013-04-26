<?php
class CountriesTranslate
{
    /**
     * CREATE TABLE countries(swedish STRING UNIQUE, english STRING UNIQUE);
     */

    private function __construct()
    {
        $dbh = new PDO('sqlite:./db/countries.db');
        $this->sthFromEnglish = $dbh->prepare('SELECT `swedish` FROM `countries` WHERE `english` LIKE ?');
        $this->sthAll = $dbh->prepare('SELECT * FROM `countries` ORDER BY `english`');
        $this->sthInsert = $dbh->prepare('INSERT OR IGNORE INTO `countries` (`english`, `swedish`) VALUES  (?, ?)');
    }

    public static function getInstance()
    {
        static $instance;
        if(!$instance)
            $instance = new CountriesTranslate();
        return $instance;
    }

    public function englishToSwedish($english)
    {
        $this->sthFromEnglish->execute(array($english));
        return $this->sthFromEnglish->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function displayAll()
    {
        $this->sthAll->execute();
        return $this->sthAll->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($english, $swedish)
    {
        $this->sthInsert->execute(array($english, $swedish));
    }
}
