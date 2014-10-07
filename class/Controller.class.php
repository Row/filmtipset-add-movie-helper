<?php
/**
*
*/
class Controller
{
    function __construct()
    {
        $action =
            isset($_REQUEST['action']) && method_exists($this, $_REQUEST['action']) ?
                $_REQUEST['action']
            :
                'viewImdbInput';
        try {
            $this->$action();
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function viewImdbInput()
    {
        $head  = new Template('template/head.html');
        $input = new Template('template/input.html');
        $input->addStringVar('formaction', 'loadImdbIntoForm');
        $foot  = new template('template/foot.html');
        header('Content-Type: text/html; charset=iso-8859-1');
        echo $head->render();
        echo $input->render();
        echo $foot->render();
    }

    private function loadImdbIntoForm()
    {
        $movie = new MovieInfo();
        $id = isset($_REQUEST['imdbid']) ?
            preg_replace('#[^\d]#', '', $_REQUEST['imdbid'])
            :
            '0000';
        $translator = Translator::getInstance();
        $movie->setImdbId($id);
        $parser = new ImdbParser($movie);
        $parser->parse();
        //var_dump('<pre>', $movie);
        $form = new Template('template/form.html');
        $form->addStringVar('title',     $movie->getTitle());
        $form->addStringVar('originaltitle', $movie->getOriginalTitle());
        $form->addStringVar('year',      $movie->getYear());
        $form->addStringVar('imdb',      $movie->getImdbId());
        $form->addStringVar('plot',      $movie->getPlot());
        $form->addStringVar('runtime',   $movie->getRuntime());
        $form->addStringVar('untranslated', json_encode($translator->getUntranslated($movie->getCountries())));
        $form->addStringVar('countries', implode(", ", $translator->getTranslated($movie->getCountries())));
        $form->addStringVar('actors',    implode(", ", $movie->getActors()));
        $form->addStringVar('writers',   implode(", ", $movie->getWriters()));
        $form->addStringVar('directors', implode(", ", $movie->getDirectors()));
        $form->addStringVar('alttitles', implode("\n", $movie->getAltTitles()));

        $head = new Template('template/head.html');
        $input = new Template('template/input.html');
        $input->addStringVar('formaction', 'loadImdbIntoForm');
        $input->addStringVar('imdbid', $movie->getImdbId());

        $foot = new template('template/foot.html');

        header('Content-Type: text/html; charset=iso-8859-1');

        print(
            $head->render() .
            $input->render() .
            $form->render() .
            $foot->render()
        );

        file_put_contents('add_log.log',
            sprintf("%s %15s %-20.20s (%4s) %12s\r\n",
                date('Y-m-d H:i:s'),
                $_SERVER['REMOTE_ADDR'],
                $movie->getTitle(),
                $movie->getYear(),
                $movie->getImdbId()), FILE_APPEND);

    }

    private function translateCountry()
    {
        if(!isset($_REQUEST['english'],$_REQUEST['swedish']))
            throw new Exception("Missing required field");

        $translator = Translator::getInstance();

        $translator->insert($_REQUEST['english'], $_REQUEST['swedish']);

        print('ok: ' . $_REQUEST['english'] . ', ' . $_REQUEST['swedish']);

    }

    private function error($message)
    {
        $head = new Template('template/head.html');
        $error = new Template('template/error.html');
        $foot = new template('template/foot.html');
        header('Content-Type: text/html; charset=iso-8859-1');
        print(
            $head->render() .
            $error->render() .
            $foot->render()
        );
    }

}
