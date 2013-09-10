<?php
class Translator
{
    /**
     * CREATE TABLE countries(swedish STRING UNIQUE, english STRING UNIQUE);
     */

    private function __construct()
    {
        $dbh = new PDO('sqlite:./db/countries.db');
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->sthFromEnglish = $dbh->prepare('SELECT `swedish` FROM `countries` WHERE `english` LIKE ?');
        $this->sthAll = $dbh->prepare('SELECT * FROM `countries` ORDER BY `english`');
        $this->sthInsert = $dbh->prepare('INSERT OR IGNORE INTO `countries` (`english`, `swedish`) VALUES  (?, ?)');
    }

    public static function getInstance()
    {
        static $instance;
        if(!$instance)
            $instance = new Translator();
        return $instance;
    }

    public function getTranslated($arrayEnglish)
    {
        $result = array();
        foreach ($arrayEnglish as $value) {
            $translate = $this->englishToSwedish($value);
            if($translate)
                $result[] = $translate;
        }
        return $result;
    }

    public function getUntranslated($arrayEnglish)
    {
        $result = array();
        foreach ($arrayEnglish as $value) {
            if(!$this->englishToSwedish($value))
                $result[] = $value;
        }
        return $result;
    }

    public function englishToSwedish($english)
    {
        $this->sthFromEnglish->execute(array($english));
        $result = $this->sthFromEnglish->fetchAll(PDO::FETCH_ASSOC);
        return count($result) ? $result[0]['swedish'] : false;
    }

    public function getAll()
    {
        $this->sthAll->execute();
        return $this->sthAll->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($english, $swedish)
    {
        $this->sthInsert->execute(array($english, $swedish));
    }
}
