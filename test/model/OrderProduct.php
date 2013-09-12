<?php

namespace Model;

use Ouzo\Model;

class OrderProduct extends Model
{
    private $_fields = array('id_order', 'id_product');

    public function __construct($attributes = array())
    {
        parent::__construct(array(
            'table' => 'order_products',
            'sequence' => 'order_products_id_order_products_seq',
            'primaryKey' => 'id_order_products',
            'attributes' => $attributes,
            'fields' => $this->_fields));
    }

}