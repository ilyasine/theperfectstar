<?php

namespace FluentCampaign\App\Modules\AbandonCart;

use FluentCrm\App\Models\Model;
use FluentCrm\App\Services\Funnel\FunnelHelper;
use FluentCrm\Framework\Support\Arr;

class AbandonCartModel extends Model
{
    protected $table = 'fc_abandoned_carts';

    protected $fillable = [
        'checkout_key',
        'cart_hash',
        'contact_id',
        'is_optout',
        'full_name',
        'email',
        'provider',
        'user_id',
        'order_id',
        'automation_id',
        'checkout_page_id',
        'status',
        'subtotal',
        'shipping',
        'discounts',
        'fees',
        'tax',
        'total',
        'currency',
        'cart',
        'note',
        'recovered_at',
        'abandoned_at',
        'click_counts'
    ];

    protected $searchable = ['full_name', 'email'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->checkout_key = md5(time() . wp_generate_uuid4());
        });
    }

    public function scopeProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeStatusBy($query, $status)
    {
        if (!$status || $status == 'all') {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeSearchBy($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('full_name', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%');
        });
    }

    public function setCartAttribute($data)
    {
        $this->attributes['cart'] = \maybe_serialize($data);
    }

    public function getCartAttribute($data)
    {
        return \maybe_unserialize($data);
    }

    public function subscriber()
    {
        return $this->belongsTo('FluentCrm\App\Models\Subscriber', 'contact_id');
    }

    public function automation()
    {
        return $this->belongsTo('FluentCrm\App\Models\Funnel', 'automation_id');
    }

    public function getAddress($type = 'billing')
    {
        $customerData = Arr::get($this->cart, 'customer_data', []);

        if (Arr::get($customerData, 'differentShipping') != 'yes') {
            $type = 'billingAddress';
        } else {
            $type = 'shippingAddress';
        }

        return array_filter([
            'address_1' => Arr::get($customerData, $type . '.address_1'),
            'address_2' => Arr::get($customerData, $type . '.address_2'),
            'city'      => Arr::get($customerData, $type . '.city'),
            'state'     => Arr::get($customerData, $type . '.state'),
            'postcode'  => Arr::get($customerData, $type . '.postcode'),
            'country'   => Arr::get($customerData, $type . '.country'),
        ]);
    }

    private function getAddressLineByKey($type, $key)
    {
        $address = $this->getAddress($type);
        return Arr::get($address, $key, '');
    }

    public function getInputProp($key, $default = '')
    {
        $customerData = Arr::get($this->cart, 'customer_data', []);

        return Arr::get($customerData, $key, $default);
    }

    public function getAddressProp($key, $addressType = 'billingAddress', $default = '')
    {
        $address = Arr::get($this->cart, 'customer_data.'.$addressType, []);

        return Arr::get($address, $key, $default);
    }

    public function getCartItemsHtml()
    {
        $cartItems = Arr::get($this->cart, 'cart_contents', []);

        $html = '<table style="border-spacing: 0;border-collapse: separate;width: 100%;border: 1px solid #D6DAE1;border-radius: 8px;">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th style="border-right:1px solid #e9ecf0;border-top-left-radius: 8px;background: #EAECF0;padding: 8px 20px;color: #323232;line-height: 26px;font-weight: 700;font-size: 14px;">' . __('Item', 'fluentcampaign-pro') . '</th>';
        $html .= '<th style="border-right:1px solid #e9ecf0;background: #EAECF0;padding: 8px 20px;color: #323232;line-height: 26px;font-weight: 700;font-size: 14px;">' . __('Quantity', 'fluentcampaign-pro') . '</th>';
        $html .= '<th style="border-top-right-radius: 8px;background: #EAECF0;padding: 8px 20px;color: #323232;line-height: 26px;font-weight: 700;font-size: 14px;">' . __('Price', 'fluentcampaign-pro') . '</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($cartItems as $cartItem) {
            $html .= '<tr>';
            $html .= '<td style="padding: 8px 20px;border-top:1px solid #e9ecf0;border-right:1px solid #e9ecf0;">' . Arr::get($cartItem, 'title') . '</td>';
            $html .= '<td style="padding: 8px 20px;border-top:1px solid #e9ecf0;border-right:1px solid #e9ecf0;">' . Arr::get($cartItem, 'quantity') . '</td>';
            $html .= '<td style="padding: 8px 20px;border-top:1px solid #e9ecf0;">' . Arr::get($cartItem, 'line_total') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }

    public function getRecoveryUrl()
    {
        if ($this->status != 'processing') {
            return '';
        }

        return add_query_arg([
            'fluentcrm'  => 1,
            'route'      => 'general',
            'handler'    => 'fc_cart_' . $this->provider,
            'fc_ab_hash' => $this->checkout_key
        ], home_url());
    }

    public function deleteCart()
    {
        if ($this->automation_id && $this->contact_id) {
            FunnelHelper::removeSubscribersFromFunnel($this->automation_id, [$this->contact_id]);
        }

        $this->delete();
    }

    public function optOut()
    {
        if ($this->is_optout) {
            return $this;
        }

        $this->is_optout = 1;
        $this->status = 'opt_out';
        $this->save();

        if (!$this->contact_id || !$this->automation_id) {
            return $this;
        }

        if ($this->status == 'processing') {
            FunnelHelper::removeSubscribersFromFunnel($this->automation_id, [$this->contact_id]);
            $this->automation_id = null;
            $this->save();
        }

        return $this;
    }
}
