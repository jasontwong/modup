<?php

class EcommerceOrder extends Doctrine_Record
{
    //{{{ public function setTableDefinition()
    public function setTableDefinition()
    {
        $this->hasColumn(
            'id', 'integer', 8,
            array(
                'primary' => TRUE,
                'autoincrement' => TRUE,
            )
        );
        $this->hasColumn(
            'order_name', 'string', 100, 
            array(
                'type' => 'string', 
                'length' => '100',
                'notnull' => TRUE,
                'unique' => TRUE,
            )
        );
        $this->hasColumn(
            'shipping', 'decimal', 18,
            array(
                'default' => 0,
                'scale' => 2,
            )
        );
        $this->hasColumn(
            'discount', 'decimal', 18,
            array(
                'default' => 0,
                'scale' => 2,
            )
        );
        $this->hasColumn(
            'gift_card_discount', 'decimal', 18,
            array(
                'default' => 0,
                'scale' => 2,
            )
        );
        $this->hasColumn(
            'tax', 'decimal', 18,
            array(
                'default' => 0,
                'scale' => 2,
            )
        );
        $this->hasColumn(
            'weight', 'decimal', 18,
            array(
                'default' => 0,
                'scale' => 2,
            )
        );
        $this->hasColumn(
            'subtotal', 'decimal', 18,
            array(
                'default' => 0,
                'scale' => 2,
            )
        );
        $this->hasColumn(
            'total', 'decimal', 18,
            array(
                'default' => 0,
                'scale' => 2,
            )
        );
        $this->hasColumn(
            'user_comments', 'string', 255, 
            array(
                'type' => 'string', 
                'default' => '',
                'length' => '255'
            )
        );
        $this->hasColumn(
            'admin_comments', 'string', 255, 
            array(
                'type' => 'string', 
                'default' => '',
                'length' => '255'
            )
        );
        $this->hasColumn(
            'pp_authorization_id', 'string', 255, 
            array(
                'type' => 'string', 
                'default' => '',
                'length' => '255'
            )
        );
        $this->hasColumn(
            'pp_transaction_id', 'string', 255, 
            array(
                'type' => 'string', 
                'default' => '',
                'length' => '255'
            )
        );
        $this->hasColumn(
            'pp_return_transaction_id', 'string', 255, 
            array(
                'type' => 'string', 
                'default' => '',
                'length' => '255'
            )
        );
        $this->hasColumn(
            'tracking_number', 'string', 255, 
            array(
                'type' => 'string', 
                'default' => '',
                'length' => '255'
            )
        );
        $this->hasColumn(
            'shipping_label', 'string', 255, 
            array(
                'type' => 'string', 
                'length' => '255',
                'default' => '',
            )
        );
        $this->hasColumn(
            'shipping_carrier', 'string', 255, 
            array(
                'type' => 'string', 
                'length' => '255',
                'default' => '',
            )
        );
        $this->hasColumn(
            'customer_email', 'string', 255, 
            array(
                'type' => 'string', 
                'default' => '',
                'length' => '255'
            )
        );
        $this->hasColumn(
            'shipped_date', 'integer', 8,
            array(
                'default' => 0,
            )
        );
        $this->hasColumn(
            'modified_date', 'integer', 8,
            array(
                'default' => 0,
            )
        );
        $this->hasColumn(
            'created_date', 'integer', 8,
            array(
                'default' => 0,
            )
        );
        $this->hasColumn('order_status_id', 'integer', 2);
        $this->hasColumn('return_status_id', 'integer', 2);
        $this->hasColumn('billing_address_id', 'integer', 8);
        $this->hasColumn('shipping_address_id', 'integer', 8);
        $this->hasColumn('returned_order_name', 'string', 100);

        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
        $this->option('type', 'MyISAM');
    }

    //}}}
    //{{{ public function setUp()
    public function setUp()
    {
        $this->hasOne(
            'EcommerceAddress as ShippingAddress',
            array(
                'foreign' => 'id',
                'local' => 'shipping_address_id',
            )
        );
        $this->hasOne(
            'EcommerceAddress as BillingAddress',
            array(
                'foreign' => 'id',
                'local' => 'billing_address_id',
            )
        );
        $this->hasOne(
            'EcommerceOrder as ReturnedOrder',
            array(
                'foreign' => 'order_name',
                'local' => 'returned_order_name',
            )
        );
        $this->hasOne(
            'EcommerceOrderStatus as Status',
            array(
                'foreign' => 'id',
                'local' => 'order_status_id',
                'onDelete' => 'SET NULL',
                'onUpdate' => 'CASCADE',
            )
        );
        $this->hasOne(
            'EcommerceOrderStatus as ReturnStatus',
            array(
                'foreign' => 'id',
                'local' => 'return_status_id',
                'onDelete' => 'SET NULL',
                'onUpdate' => 'CASCADE',
            )
        );
        $this->hasMany(
            'EcommerceCoupon as Coupons',
            array(
                'local' => 'order_id',
                'foreign' => 'coupon_id',
                'refClass' => 'EcommerceOrderCoupons',
            )
        );
        $this->hasMany(
            'EcommerceGiftCard as GiftCards',
            array(
                'local' => 'order_id',
                'foreign' => 'gift_card_id',
                'refClass' => 'EcommerceOrderGiftCards',
            )
        );
        $this->hasMany(
            'EcommerceOption as Options',
            array(
                'foreign' => 'order_id',
                'local' => 'id',
            )
        );
        $this->hasMany(
            'EcommerceProduct as Products',
            array(
                'foreign' => 'order_id',
                'local' => 'id',
            )
        );
    }

    //}}}

    // {{{ public function preInsert($event)
    public function preInsert($event)
    {
        if (!is_numeric($this->created_date) || $this->created_date === 0)
        {
            $this->created_date = time();
        }
        $this->modified_date = $this->created_date;
    }
    // }}}
    // {{{ public function preUpdate($event)
    public function preUpdate($event)
    {
        $this->modified_date = time();
    }
    // }}}
}

?>
