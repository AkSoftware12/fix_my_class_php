<?php

namespace App\Services;

use App\Models\City;
use App\Repositories\CityRepository;

class CityService
{
    public function __construct(protected CityRepository $cities)
    {
    }

    public function create(array $data): City
    {
        return $this->cities->create(collect($data)->only(['name', 'state', 'is_active'])->all());
    }

    public function update(City $city, array $data): City
    {
        return $this->cities->update($city, collect($data)->only(['name', 'state', 'is_active'])->all());
    }

    public function delete(City $city): void
    {
        $this->cities->delete($city);
    }
}
