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
        $controller_collection->get('/', [ $this, 'executeIndex' ])->bind('main_index');
        $controller_collection->get('/navbar', [ $this, 'executeNavbar' ])->bind('main_navbar');
        $controller_collection->get('/fabrik/pomm', [ $this, 'executeFabrikPomm' ])->bind('main_fabrik_pomm');
        $controller_collection->get('/about', [ $this, 'executeAbout' ])->bind('main_about');
        $controller_collection->get('/contact', [ $this, 'executeContact' ])->bind('main_contact');
        $controller_collection->get('/legal', [ $this, 'executeLegal' ])->bind('main_legal');
        $controller_collection->post('/contact', [ $this, 'executePostContact' ])->bind('main_post_contact');
        $controller_collection->get('/service/audit', [ $this, 'executeServiceAudit' ])->bind('main_service_audit');
        $controller_collection->get('/service/backing', [ $this, 'executeServiceBacking' ])->bind('main_service_backing');
        $controller_collection->get('/service/training', [ $this, 'executeServiceTraining' ])->bind('main_service_training');
        $controller_collection->get('/service/support', [ $this, 'executeServiceSupport' ])->bind('main_service_support');

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

    public function executeServiceAudit($lang)
    {
        return $this->app['twig']->render(sprintf("%s/service_audit.html.twig", $lang));
    }

    public function executeServiceBacking($lang)
    {
        return $this->app['twig']->render(sprintf("%s/service_backing.html.twig", $lang));
    }

    public function executeServiceTraining($lang)
    {
        return $this->app['twig']->render(sprintf("%s/service_training.html.twig", $lang));
    }

    public function executeServiceSupport($lang)
    {
        return $this->app['twig']->render(sprintf("%s/service_support.html.twig", $lang));
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
            ;

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
                    ->setFrom("noreply@pragmafabrik.com")
                    ->setSender($this->app['config.swiftmailer']['destination'])
                    ->setReplyTo([$values['email']])
                    ->setTo([$this->app['config.swiftmailer']['destination']])
                    ->setBody(sprintf("from: %s %s (%s)\n\n%s", $values['email'], $values['name'], $values['company'], $values['message']))
                );
            $flash_message = [ 'fr' =>  'Votre demande de contact a été envoyée.', 'en' => 'Your contact request has been sent.' ];
            $this->app['session']->getFlashBag()->add('success', $flash_message[$this->app['request']->get('lang', 'fr')]);

            return $this->app->redirect($this->app['url_generator']->generate('main_contact'));
        }

        $flash_message = [ 'fr' =>  'Votre demande de contact n\'a pu aboutir.', 'en' => 'Your contact query could not be fulfilled.' ];
        $this->app['session']->getFlashBag()->add('error', $flash_message[$this->app['request']->get('lang', 'fr')]);

        return $this->app['twig']->render(sprintf("%s/contact.html.twig", $lang), [ 'form' => $form->createView() ]);
    }
    public function executeLegal($lang)
    {
        return $this->app['twig']->render(sprintf("%s/legal.html.twig", $lang));
    }

}
