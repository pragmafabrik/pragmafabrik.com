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
        $controller_collection->get('/', [ $this, 'executeIndex' ])->bind('main_index')->value('lang', 'fr');
        $controller_collection->get('/navbar', [ $this, 'executeNavbar' ])->bind('main_navbar')->value('lang', 'fr');
        $controller_collection->get('/fabrik/pomm', [ $this, 'executeFabrikPomm' ])->bind('main_fabrik_pomm')->value('lang', 'fr');
        $controller_collection->get('/about', [ $this, 'executeAbout' ])->bind('main_about')->value('lang', 'fr');

        return $controller_collection;
    }

    public function executeIndex($lang)
    {
        return $this->app["twig"]->render(sprintf("%s/index.html.twig", $lang));
    }

    public function executeNavbar($lang)
    {
        return $this->app["twig"]->render(sprintf("%s/_navbar.html.twig", $lang));
    }

    public function executeFabrikPomm($lang)
    {
        return $this->app["twig"]->render(sprintf("%s/fabrik_pomm.html.twig", $lang));
    }

    public function executeAbout($lang)
    {
        return $this->app['twig']->render(sprintf("%s/about.html.twig", $lang));
    }

    public function executeContact($lang)
    {
        $form = $this->app['form.factory']
            ->createBuilder(new \Form\Contact())
            ->getForm()
            ->bind($this->app['request']);

        return $this->app['twig']->render(sprintf("%s/contact.html.twig", $lang), [ 'form' => $form->createView() ]);
    }

    public function executePostContact($lang)
    {
        $form = $this->app['form.factory']
            ->createBuilder(new \Form\Contact())
            ->getForm()
            ->bind($this->app['request']);

        if ($form->isValid())
        {
            $values = $form->getData();
            $this->app['mailer']->send(
                \Swift_Message::newInstance()
                    ->setSubject(sprintf('[pragmafabrik.com] Demande de contact (%s).', $values['name']))
                    ->setFrom(array($values['email'] => $values['name']))
                    ->setReplyTo(array($values['email'] => $values['name']))
                    ->setTo(array($this->app['config.swiftmailer']['destination']))
                    ->setBody($values['message'])
                );
            $flash_message = [ 'fr' =>  'Votre demande de contact a été envoyée.', 'en' => 'Your contact request has been sent.' ];
            $this->app['session']->getFlashBag()->add('success', $flash_message[$this->app['request']->get('lang', 'fr')]);

            return $this->app->redirect($this->app['url_generator']->generate('main_index'));
        }

        $flash_message = [ 'fr' =>  'Votre demande de contact n\'a pu aboutir.', 'en' => 'Your contact query could not be fulfilled.' ];
        $this->app['session']->getFlashBag()->add('error', $flash_message[$this->app['request']->get('lang', 'fr')]);

        return $this->executeContact($lang);
    }
}
