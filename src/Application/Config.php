<?php declare(strict_types=1);

namespace Igni\Application;

/**
 * Application's config container.
 * Treats dots as an operator for accessing nested values.
 * If constant name is put in curly braces as a value, it wil be replaced
 * to the constant value.
 *
 * @example:
 * // Example usage.
 * $config = new Config();
 * $config->set('some.key', true);
 * $some = $config->get('some'); // returns ['key' => true]
 *
 * @package Igni\Application
 */
class Config
{
    /**
     * @var array
     */
    private $config;

    /**
     * Config constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Checks if config key exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->lookup($key) !== null;
    }

    private function lookup(string $key)
    {
        $result = $this->config;
        $key = explode('.', $key);
        foreach($key as $part) {
            if (!is_array($result) || !isset($result[$part])) {
                return null;
            }
            $result = $result[$part];
        }

        return $result;
    }

    /**
     * Gets value behind the key, or returns $default value if path does not exists.
     *
     * @param string $key
     * @param null $default
     * @return null|string|string[]
     */
    public function get(string $key, $default = null)
    {
        $result = $this->lookup($key);
        return $result === null ? $default : $this->fetchConstants($result);
    }

    /**
     * Merges one instance of Config class into current one and
     * returns current instance.
     *
     * @param Config $config
     * @return Config
     */
    public function merge(Config $config): Config
    {
        $this->config = array_merge_recursive($this->config, $config);

        return $this;
    }

    /**
     * Sets new value.
     *
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value): void
    {
        $key = explode('.', $key);
        $last = array_pop($key);
        $result = &$this->config;

        foreach ($key as $part) {
            if (!isset($result[$part]) || !is_array($result[$part])) {
                $result[$part] = [];
            }
            $result = &$result[$part];
        }
        $result[$last] = $value;
    }

    /**
     * Returns array representation of the config.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->config;
    }

    private function fetchConstants($value)
    {
        if (!is_string($value)) {
            return $value;
        }
        return preg_replace_callback(
            '#\$\{([^{}]*)\}#',
            function($matches) {
                if (defined($matches[1])) {
                    return constant($matches[1]);
                }
                return $matches[0];
            },
            $value
        );
    }
}
