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
        if(!$this->data)
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

    //<title>Megamind (2010) - IMDb</title>
    private function parseTitle()
    {
        if(preg_match('#<title>(.*?) \(\d{4}\) - IMDb#', $this->data, $m))
            $this->movieInfo->setTitle($m[1]);
    }
    
    /*
        <span class="title-extra">
The Lord of the Rings: The Fellowship of the Ring 
<i>(original title)</i>
</span>

    */    
    private function parseOriginalTitle()
    {
        if(preg_match('#<span class="title-extra">\n([^\n]+)#', $this->data, $m))
            $this->movieInfo->setOriginalTitle($m[1]);
        else
            $this->movieInfo->setOriginalTitle($this->movieInfo->getTitle());
    }

    private function parseYear()
    {
        if(preg_match('#<title>.*? \((\d{4})\) - IMDb#', $this->data, $m))
            $this->movieInfo->setYear($m[1]);
    }

    /*
        Director:
  </h4>
<a  href="/name/nm0569891/">Tom McGrath</a></div>
    */
    private function parseDirectors()
    {
        $query = "descendant-or-self::a[@itemprop = 'director']";
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
        $query = "descendant-or-self::h4[contains(text(), 'Writer')]/../a[contains(@href, 'name')]";   
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
        $query = "descendant-or-self::table[contains(concat(' ', normalize-space(@class), ' '), ' cast_list ')]/descendant::td[contains(concat(' ', normalize-space(@class), ' '), ' name ')]/descendant::a";   
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
/*
    Also Known As:</h4> Megaagy
        */
    
    private function parseAltTitles()
    {
        if(preg_match('#Also Known As:</h4>(.*?)<span#s', $this->data, $m))
            if(preg_match_all('#^.*?$#', $m[1], $m1))
                foreach($m1[0] as $var)
                    $this->movieInfo->addAltTitle($var);

    }

    /*
        <h2>Storyline</h2>

<p>After super-villain Megamind (Ferrell) kills his good-guy nemesis, Metro Man (Pitt), he becomes bored since there is no one left to fight. He creates a new foe, Titan (Hill), who, instead of using his powers for good, sets out to destroy the world, positioning Megamind to save the day for the first time in his life.

<em
    */

    private function parsePlot()
    {
        if(preg_match('#<h2>Storyline</h2>.*?<p>([^<]+)#s', $this->data, $m))
            $this->movieInfo->setPlot($m[1]);

    }

/*
    Runtime:</h4> 


96 min

</div>

*/

    private function parseRuntime()
    {
        if(preg_match('#Runtime:</h4>.*?(\d+).*?</div>#s', $this->data, $m))
            $this->movieInfo->setRuntime($m[1]);
    }
}
