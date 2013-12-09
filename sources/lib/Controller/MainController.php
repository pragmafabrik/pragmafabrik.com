<?php
namespace Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class MainController implements ControllerProviderInterface
{
    protected $app;

    public function connect(Application $app)
    {
        $this->app = $app;
        $controller_collection = $app['controllers_factory'];
        $controller_collection->get('/', array($this, 'executeIndex'))->bind('main_index')->value('lang', 'fr');
        $controller_collection->get('/navbar', array($this, 'executeNavbar'))->bind('main_navbar')->value('lang', 'fr');
        $controller_collection->get('/who', array($this, 'executeWho'))->bind('main_who')->value('lang', 'fr');
        $controller_collection->get('/what', array($this, 'executeWhat'))->bind('main_what')->value('lang', 'fr');
        $controller_collection->get('/informations', array($this, 'executeInformations'))->bind('main_informations')->value('lang', 'fr');

        return $controller_collection;
    }

    public function executeIndex()
    {
        return $this->app["twig"]->render(sprintf("index_%s.html.twig", $this->getLang()));
    }

    public function executeNavbar()
    {
        return $this->app["twig"]->render(sprintf("_navbar_%s.html.twig", $this->getLang()));
    }

    protected function getLang()
    {
        if ($this->app['request']->query->has('lang'))
        {
            $lang = $this->app['request']->query->get('lang');
            $lang = in_array($lang, array('en')) ? $lang : 'fr';
            $this->app['session']->set('culture', $lang);
        }
        elseif (!$this->app['session']->has('culture'))
        {
            $this->app['session']->set('culture', 'fr');
        }

        return $this->app['session']->get('culture');
    }
}
