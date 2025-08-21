<?php

namespace Fereydooni\Shopping\app\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class GeographicDataService
{
    protected array $config;
    protected array $defaultValues;

    public function __construct()
    {
        $this->config = Config::get('shopping.geographic_models', [
            'country_model' => 'App\Models\Country',
            'province_model' => 'App\Models\Province',
            'county_model' => 'App\Models\County',
            'city_model' => 'App\Models\City',
            'village_model' => 'App\Models\Village',
        ]);

        $this->defaultValues = Config::get('shopping.geographic_defaults', [
            'country' => 'Iran',
            'province' => 'Tehran',
            'county' => 'Tehran',
            'city' => 'Tehran',
            'village' => null,
        ]);
    }

    public function getModel(string $type): string
    {
        $key = $type . '_model';
        return $this->config[$key] ?? "App\\Models\\" . ucfirst($type);
    }

    public function getDefaultValue(string $type): mixed
    {
        return $this->defaultValues[$type] ?? null;
    }

    public function getCountries(): Collection
    {
        return Cache::remember('geographic_countries', 3600, function () {
            $model = $this->getModel('country');
            return app($model)->orderBy('name')->get();
        });
    }

    public function getProvinces(?int $countryId = null): Collection
    {
        $cacheKey = 'geographic_provinces_' . ($countryId ?? 'all');

        return Cache::remember($cacheKey, 3600, function () use ($countryId) {
            $model = $this->getModel('province');
            $query = app($model)->orderBy('name');

            if ($countryId) {
                $query->where('country_id', $countryId);
            }

            return $query->get();
        });
    }

    public function getCounties(?int $provinceId = null): Collection
    {
        $cacheKey = 'geographic_counties_' . ($provinceId ?? 'all');

        return Cache::remember($cacheKey, 3600, function () use ($provinceId) {
            $model = $this->getModel('county');
            $query = app($model)->orderBy('name');

            if ($provinceId) {
                $query->where('province_id', $provinceId);
            }

            return $query->get();
        });
    }

    public function getCities(?int $countyId = null): Collection
    {
        $cacheKey = 'geographic_cities_' . ($countyId ?? 'all');

        return Cache::remember($cacheKey, 3600, function () use ($countyId) {
            $model = $this->getModel('city');
            $query = app($model)->orderBy('name');

            if ($countyId) {
                $query->where('county_id', $countyId);
            }

            return $query->get();
        });
    }

    public function getVillages(?int $cityId = null): Collection
    {
        $cacheKey = 'geographic_villages_' . ($cityId ?? 'all');

        return Cache::remember($cacheKey, 3600, function () use ($cityId) {
            $model = $this->getModel('village');
            $query = app($model)->orderBy('name');

            if ($cityId) {
                $query->where('city_id', $cityId);
            }

            return $query->get();
        });
    }

    public function getGeographicHierarchy(): array
    {
        return Cache::remember('geographic_hierarchy', 3600, function () {
            return [
                'countries' => $this->getCountries(),
                'defaults' => $this->defaultValues,
            ];
        });
    }

    public function validateGeographicData(array $data): array
    {
        $errors = [];

        // Validate country
        if (isset($data['country_id']) && $data['country_id']) {
            $countryModel = $this->getModel('country');
            if (!app($countryModel)->find($data['country_id'])) {
                $errors['country_id'] = 'Selected country does not exist';
            }
        }

        // Validate province
        if (isset($data['province_id']) && $data['province_id']) {
            $provinceModel = $this->getModel('province');
            $province = app($provinceModel)->find($data['province_id']);
            if (!$province) {
                $errors['province_id'] = 'Selected province does not exist';
            } elseif (isset($data['country_id']) && $data['country_id'] && $province->country_id != $data['country_id']) {
                $errors['province_id'] = 'Selected province does not belong to the selected country';
            }
        }

        // Validate county
        if (isset($data['county_id']) && $data['county_id']) {
            $countyModel = $this->getModel('county');
            $county = app($countyModel)->find($data['county_id']);
            if (!$county) {
                $errors['county_id'] = 'Selected county does not exist';
            } elseif (isset($data['province_id']) && $data['province_id'] && $county->province_id != $data['province_id']) {
                $errors['county_id'] = 'Selected county does not belong to the selected province';
            }
        }

        // Validate city
        if (isset($data['city_id']) && $data['city_id']) {
            $cityModel = $this->getModel('city');
            $city = app($cityModel)->find($data['city_id']);
            if (!$city) {
                $errors['city_id'] = 'Selected city does not exist';
            } elseif (isset($data['county_id']) && $data['county_id'] && $city->county_id != $data['county_id']) {
                $errors['city_id'] = 'Selected city does not belong to the selected county';
            }
        }

        // Validate village
        if (isset($data['village_id']) && $data['village_id']) {
            $villageModel = $this->getModel('village');
            $village = app($villageModel)->find($data['village_id']);
            if (!$village) {
                $errors['village_id'] = 'Selected village does not exist';
            } elseif (isset($data['city_id']) && $data['city_id'] && $village->city_id != $data['city_id']) {
                $errors['village_id'] = 'Selected village does not belong to the selected city';
            }
        }

        return $errors;
    }

    public function getDefaultGeographicData(): array
    {
        $defaults = [];

        // Get default country
        if ($this->defaultValues['country']) {
            $countryModel = $this->getModel('country');
            $defaults['country'] = app($countryModel)->where('name', $this->defaultValues['country'])->first();
        }

        // Get default province
        if ($this->defaultValues['province'] && isset($defaults['country'])) {
            $provinceModel = $this->getModel('province');
            $defaults['province'] = app($provinceModel)
                ->where('name', $this->defaultValues['province'])
                ->where('country_id', $defaults['country']->id)
                ->first();
        }

        // Get default county
        if ($this->defaultValues['county'] && isset($defaults['province'])) {
            $countyModel = $this->getModel('county');
            $defaults['county'] = app($countyModel)
                ->where('name', $this->defaultValues['county'])
                ->where('province_id', $defaults['province']->id)
                ->first();
        }

        // Get default city
        if ($this->defaultValues['city'] && isset($defaults['county'])) {
            $cityModel = $this->getModel('city');
            $defaults['city'] = app($cityModel)
                ->where('name', $this->defaultValues['city'])
                ->where('county_id', $defaults['county']->id)
                ->first();
        }

        return $defaults;
    }

    public function clearCache(): void
    {
        Cache::forget('geographic_countries');
        Cache::forget('geographic_hierarchy');

        // Clear province caches
        Cache::forget('geographic_provinces_all');
        $this->getCountries()->each(function ($country) {
            Cache::forget('geographic_provinces_' . $country->id);
        });

        // Clear county caches
        Cache::forget('geographic_counties_all');
        $this->getProvinces()->each(function ($province) {
            Cache::forget('geographic_counties_' . $province->id);
        });

        // Clear city caches
        Cache::forget('geographic_cities_all');
        $this->getCounties()->each(function ($county) {
            Cache::forget('geographic_cities_' . $county->id);
        });

        // Clear village caches
        Cache::forget('geographic_villages_all');
        $this->getCities()->each(function ($city) {
            Cache::forget('geographic_villages_' . $city->id);
        });
    }
}
