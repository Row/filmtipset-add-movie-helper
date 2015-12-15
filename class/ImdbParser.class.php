<?php
/**
*
*/
class ImdbParser
{
    private $data;
    private $movieInfo;
    private $xpath;


    function __construct(MovieInfo $movieInfo)
    {
        $this->movieInfo = $movieInfo;
        $url = sprintf('http://www.imdb.com/title/tt%s/', $movieInfo->getImdbId());
        $this->data = file_get_contents($url);
        if (!$this->data)
            throw new Exception("Unable to load movie data from IMDb");
        $htmlDom = DOMDocument::loadHTML($this->data);
        $this->xpath = new DOMXpath($htmlDom);

    }

    public function parse()
    {
        $this->parseTitle();
        $this->parseOriginalTitle();
        $this->parseYear();
        $this->parseDirectors();
        $this->parseWriters();
        $this->parseCountries();
        $this->parseActors();
        $this->parseAltTitles();
        $this->parsePlot();
        $this->parseRuntime();
    }

    private function parseTitle()
    {
        if (preg_match('#<title>(.*?) \([^)]*\d{4}\) - IMDb#', $this->data, $m))
            $this->movieInfo->setTitle($m[1]);
    }

    private function parseOriginalTitle()
    {
        $query = "//span[@class='title-extra']";
        $entries = $this->xpath->evaluate($query);
        if ($entries && $entries->length && preg_match('#^[\s\n]+"(.*?)"#', $entries->item(0)->nodeValue, $m))
            $this->movieInfo->setOriginalTitle($m[1]);
        else
            $this->movieInfo->setOriginalTitle($this->movieInfo->getTitle());
    }

    private function parseYear()
    {
        if (preg_match('#<title>.*?(\d{4})\) - IMDb#', $this->data, $m))
            $this->movieInfo->setYear($m[1]);
    }

    private function parseDirectors()
    {
        $query = "descendant-or-self::*[@itemprop='director']/a/span[@itemprop='name']";
        $entries = $this->xpath->query($query);
        foreach ($entries as $entry) {
            $this->movieInfo->addDirector($entry->nodeValue);
        }
    }
    /*
        XXX Correct type?
    */
    private function parseWriters()
    {
        $query = "descendant-or-self::h4[contains(text(), 'Writer')]/../span/a/span[@itemprop='name']";
        $entries = $this->xpath->query($query);
        foreach ($entries as $entry) {
            $this->movieInfo->addWriter($entry->nodeValue);
        }
    }

    /*
        <h2>Cast</h2>.*?</table>
        XXX Voice?
    */
    private function parseActors()
    {
        $query = "descendant-or-self::table[@class = 'cast_list']/tr/td[@itemprop = 'actor']/a/span[@itemprop = 'name']";
        $entries = $this->xpath->query($query);
        foreach ($entries as $entry) {
            $this->movieInfo->addActor($entry->nodeValue);
        }

    }

    private function parseCountries()
    {
        $query = "descendant-or-self::h4[contains(text(), 'Count')]/../a[contains(@href, 'country')]";
        $entries = $this->xpath->query($query);
        foreach ($entries as $entry) {
            $this->movieInfo->addCountry($entry->nodeValue);
        }
    }

    private function parseAltTitles()
    {
        if (preg_match('#Also Known As:</h4>(.*?)<span#s', $this->data, $m))
            if (preg_match_all('#^.*?$#', $m[1], $m1))
                foreach ($m1[0] as $var)
                    $this->movieInfo->addAltTitle($var);
    }

    private function parsePlot()
    {
        if (preg_match('#<h2>Storyline</h2>.*?<p>([^<]+)#s', $this->data, $m))
            $this->movieInfo->setPlot($m[1]);

    }

    private function parseRuntime()
    {
        $query = "//time[@itemprop='duration']";
        if ($entries = $this->xpath->evaluate($query))
            if ($min = preg_replace('#[^\d]+#', '', $entries->item(0)->getAttribute('datetime')))
                $this->movieInfo->setRuntime($min);
    }
}
