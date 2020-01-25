<?php

declare(strict_types=1);

namespace PoconoSewVac\AllOrdersView\Admin\Orders;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

/**
 * All orders page in dashboard
 */
class All extends Page
{
    public $key = 'orders/all';
    public $title = 'commerce.orders';
    public static $permissions = ['commerce', 'commerce_orders'];

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        $section = new SimpleSection($this->commerce, [
            'title' => $this->getTitle()
        ]);

        $section->addWidget(new Grid($this->commerce));
        $this->addSection($section);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return parent::getTitle() . ' &raquo; ' . $this->adapter->lexicon('commerce_allordersview.all');
    }
}
