<?php

namespace Obelaw\Basketin\Cart\Services;

use Obelaw\Basketin\Cart\Models\Cart;


class FieldService
{
    /**
     * @var array<string, mixed>
     */
    private array $fields = [];

    /**
     * FieldService constructor.
     */
    public function __construct(
        private Cart $cart
    ) {
        $this->refresh();
    }

    /**
     * Reload fields from the database.
     */
    public function refresh(): void
    {
        $this->fields = $this->cart->fields()->get()->pluck('field_value', 'field_key')->toArray();
    }

    /**
     * Get all fields.
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->fields;
    }

    /**
     * Set a field value.
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set(string $key, mixed $value): mixed
    {
        if ($field = $this->cart->fields()->where('field_key', $key)->first()) {
            $field->field_value = $value;
            $field->save();
            $this->refresh();
            return $field;
        }
        $created = $this->cart->fields()->create([
            'field_key' => $key,
            'field_value' => $value,
        ]);
        $this->refresh();
        return $created;
    }

    /**
     * Get a field value.
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->fields[$key] ?? $default;
    }

    /**
     * Check if a field exists.
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->fields[$key]);
    }

    /**
     * Remove a field.
     * @param string $key
     * @return int Number of deleted records
     */
    public function remove(string $key): int
    {
        $deleted = $this->cart->fields()->where('field_key', $key)->delete();
        $this->refresh();
        return $deleted;
    }
}
