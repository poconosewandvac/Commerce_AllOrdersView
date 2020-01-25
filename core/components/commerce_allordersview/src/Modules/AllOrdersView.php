<?php

declare(strict_types=1);

namespace PoconoSewVac\AllOrdersView\Modules;

use modmore\Commerce\Admin\Configuration\About\ComposerPackages;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Widgets\Form\DescriptionField;
use modmore\Commerce\Events\Admin\PageEvent;
use modmore\Commerce\Modules\BaseModule;
use modmore\Commerce\Events\Admin\GeneratorEvent;
use modmore\Commerce\Events\Admin\TopNavMenu as TopNavMenuEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

class AllOrdersView extends BaseModule {

    public function getName()
    {
        $this->adapter->loadLexicon('commerce_allordersview:default');
        return $this->adapter->lexicon('commerce_allordersview');
    }

    public function getAuthor()
    {
        return 'Tony Klapatch';
    }

    public function getDescription()
    {
        return $this->adapter->lexicon('commerce_allordersview.description');
    }

    public function initialize(EventDispatcher $dispatcher)
    {
        // Load our lexicon
        $this->adapter->loadLexicon('commerce_allordersview:default');

        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_INIT_GENERATOR, [$this, 'loadPages']);
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_GET_MENU, [$this, 'loadMenuItem']);
    }

    public function loadPages(GeneratorEvent $event)
    {
        $generator = $event->getGenerator();

        $generator->addPage('orders/all', \PoconoSewVac\AllOrdersView\Admin\Orders\All::class);
    }


    public function loadMenuItem(TopNavMenuEvent $event)
    {
        $items = $event->getItems();

        array_unshift($items['orders']['submenu'], [
            'name' => $this->adapter->lexicon('commerce_allordersview.all'),
            'key' => 'orders/all',
            'link' => $this->adapter->makeAdminUrl('orders/all'),
        ]);

        $event->setItems($items);
    }

    public function getModuleConfiguration(\comModule $module)
    {
        return [];
    }
}
