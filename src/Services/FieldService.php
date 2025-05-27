<?php

namespace Obelaw\Basketin\Cart\Services;

use Obelaw\Basketin\Cart\Models\Cart;

class FieldService
{
    private $fields = [];

    public function __construct(
        private Cart $cart
    ) {
        $this->fields = $this->cart->fields()->get()->pluck('field_value', 'field_key')->toArray();
    }

    public function all()
    {
        return $this->fields;
    }

    public function set($key, $value)
    {
        if ($field = $this->cart->fields()->where('field_key', $key)->first()) {
            $field->field_value = $value;
            $field->save();
            return $field;
        }

        return $this->cart->fields()->create([
            'field_key' => $key,
            'field_value' => $value,
        ]);
    }

    public function get($key, $default = null)
    {
        return $this->fields[$key] ?? $default;
    }

    public function has($key)
    {
        return isset($this->fields[$key]);
    }

    public function remove($key)
    {
        return $this->cart->fields()->where('field_key', $key)->delete();
    }
}
