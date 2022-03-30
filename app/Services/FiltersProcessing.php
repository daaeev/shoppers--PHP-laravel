<?php

namespace App\Services;

class FiltersProcessing implements Interfaces\FilterProcessingInterface
{
    /**
     * @var string[] зарезервированные имена для фильтров
     */
    protected $filters = [
        'filt_category',
        'filt_color',
        'filt_size',
        'order'
    ];

    /**
     * @inheritDoc
     */
    public function arrayHasFilters(array $data): bool
    {
        foreach ($this->filters as $filt_name) {
            if (array_key_exists($filt_name, $data)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getFiltersFromArray(array $data): array
    {
        $filters_array = [];

        foreach ($this->filters as $filt_name) {
            if (array_key_exists($filt_name, $data)) {
                $filters_array[$filt_name] = $data[$filt_name];
            }
        }

        return $filters_array;
    }

    /**
     * @inheritDoc
     */
    public function processFiltersArray(array $filters): array
    {
        $result = [];

        foreach ($filters as $key => $value) {
            if (str_contains($key, 'filt_')) {
                $new_key_name = str_replace('filt_', '', $key) . '_id';
                $result['where'][$new_key_name] = $value;
            } else if ($key == 'order') {
                list($result[$key]['column'], $result[$key]['sort']) = explode('_', $value);
            }
        }

        return $result;
    }
}
