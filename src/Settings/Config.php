<?php

namespace Obelaw\Basketin\Cart\Settings;

class Config
{
    /**
     * @var array<string, mixed>
     */
    private array $config = [];

    /**
     * Config constructor.
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_replace_recursive($this->getDefaultConfig(), $config);
    }

    /**
     * Get default configurations
     * @return array<string, mixed>
     */
    public function getDefaultConfig(): array
    {
        return [
            'limit_quote' => config('basketin.cart.limit_quote', 5),
        ];
    }

    /**
     * Get all configuration values
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->config;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }
}
