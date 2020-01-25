<?php

declare(strict_types=1);

namespace PoconoSewVac\AllOrdersView\Admin\Orders;

use modmore\Commerce\Admin\Util\Action;
use modmore\Commerce\Admin\Util\Column;
use modmore\Commerce\Admin\Widgets\GridWidget;
use modmore\Commerce\Admin\Orders\Grid as OrderGrid;

/**
 * All orders grid to view & search all orders in Commerce
 */
class Grid extends OrderGrid
{
    public $key = 'order-all-table';
    // Since orders may have never been received, default to created_on
    public $defaultSort = 'created_on';

    /**
     * Override to remove check for state of an order
     * 
     * @inheritDoc
     */
    public function getItems(array $options = array())
    {
        $items = [];

        $c = $this->adapter->newQuery('comOrder');
        $c->where([
            'test' => $this->commerce->isTestMode(),
        ]);

        // Find an exact or fuzzy match by the ID
        if (array_key_exists('search_by_id', $options) && $options['search_by_id'] > 0) {
            $c->where([
                'id' => (int)$options['search_by_id'],
                'OR:id:LIKE' => '%' . $options['search_by_id'] . '%',
                'OR:reference:LIKE' => '%' . $options['search_by_id'] . '%',
            ]);
        }

        if (array_key_exists('search_by_address', $options) && strlen($options['search_by_address']) > 0) {
            $this->_filteredAddress = $addressSearch = $options['search_by_address'];

            $c->leftJoin('comOrderAddress', 'Address', 'comOrder.id = Address.order');
            $c->where([
                'Address.fullname:LIKE' => "%{$addressSearch}%",
                'OR:Address.firstname:LIKE' => "%{$addressSearch}%",
                'OR:Address.lastname:LIKE' => "%{$addressSearch}%",
                'OR:Address.company:LIKE' => "%{$addressSearch}%",
                'OR:Address.address1:LIKE' => "%{$addressSearch}%",
                'OR:Address.address2:LIKE' => "%{$addressSearch}%",
                'OR:Address.address3:LIKE' => "%{$addressSearch}%",
                'OR:Address.zip:LIKE' => "%{$addressSearch}%",
                'OR:Address.city:LIKE' => "%{$addressSearch}%",
                'OR:Address.state:LIKE' => "%{$addressSearch}%",
            ]);
        }

        // Filter on the status
        if (array_key_exists('status', $options) && $options['status'] > 0) {
            $c->where([
                'status' => (int)$options['status']
            ]);
        }

        // Filter on the context
        if (array_key_exists('context', $options) && $options['context'] !== '') {
            $c->where([
                'context' => (string)$options['context']
            ]);
        }

        $sortby = array_key_exists('sortby', $options) && !empty($options['sortby']) ? $this->adapter->escape($options['sortby']) : $this->defaultSort;
        $sortdir = array_key_exists('sortdir', $options) && strtoupper($options['sortdir']) === 'ASC' ? 'ASC' : 'DESC';
        $c->sortby($sortby, $sortdir);

        $count = $this->adapter->getCount('comOrder', $c);
        $this->setTotalCount($count);

        $c->limit($options['limit'], $options['start']);
        /** @var \comOrder[] $collection */
        $collection = $this->adapter->getCollection('comOrder', $c);

        foreach ($collection as $order) {
            $items[] = $this->prepareItem($order);
        }

        return $items;
    }

    /**
     * Status override to look for all statuses available
     * 
     * @inheritDoc
     */
    protected function _getStatusOptions()
    {
        $return = [];

        // Grab the configured statuses for the current state
        $c = $this->adapter->newQuery('comStatus');
        // Original class was looking for statuses assigned to the state. This is overriden to not look for specific states
        $c->sortby('sortorder', 'ASC');
        $c->sortby('name', 'ASC');
        $c->limit(0);

        /** @var \comStatus[] $statuses */
        $statuses = $this->adapter->getCollection('comStatus', $c);

        foreach ($statuses as $status) {
            $return[] = [
                'value' => $status->get('id'),
                'label' => $status->get('name')
            ];
        }

        return $return;
    }
}
