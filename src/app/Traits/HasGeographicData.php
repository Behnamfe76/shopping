<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

trait HasGeographicData
{
    protected function getGeographicModel(string $type): string
    {
        $config = Config::get('shopping.geographic_models', [
            'country_model' => 'App\Models\Country',
            'province_model' => 'App\Models\Province',
            'county_model' => 'App\Models\County',
            'city_model' => 'App\Models\City',
            'village_model' => 'App\Models\Village',
        ]);

        $key = $type . '_model';
        return $config[$key] ?? "App\\Models\\" . ucfirst($type);
    }

    protected function getDefaultGeographicValue(string $type): mixed
    {
        $defaults = Config::get('shopping.geographic_defaults', [
            'country' => 'Iran',
            'province' => 'Tehran',
            'county' => 'Tehran',
            'city' => 'Tehran',
            'village' => null,
        ]);

        return $defaults[$type] ?? null;
    }

    public function scopeByCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeByProvince($query, int $provinceId)
    {
        return $query->where('province_id', $provinceId);
    }

    public function scopeByCounty($query, int $countyId)
    {
        return $query->where('county_id', $countyId);
    }

    public function scopeByCity($query, int $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeByVillage($query, int $villageId)
    {
        return $query->where('village_id', $villageId);
    }

    public function scopeByGeographicHierarchy($query, ?int $countryId = null, ?int $provinceId = null, ?int $countyId = null, ?int $cityId = null)
    {
        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        if ($countyId) {
            $query->where('county_id', $countyId);
        }

        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        return $query;
    }

    public function getGeographicData(): array
    {
        $data = [];

        if ($this->country_id) {
            $countryModel = $this->getGeographicModel('country');
            $data['country'] = app($countryModel)->find($this->country_id);
        }

        if ($this->province_id) {
            $provinceModel = $this->getGeographicModel('province');
            $data['province'] = app($provinceModel)->find($this->province_id);
        }

        if ($this->county_id) {
            $countyModel = $this->getGeographicModel('county');
            $data['county'] = app($countyModel)->find($this->county_id);
        }

        if ($this->city_id) {
            $cityModel = $this->getGeographicModel('city');
            $data['city'] = app($cityModel)->find($this->city_id);
        }

        if ($this->village_id) {
            $villageModel = $this->getGeographicModel('village');
            $data['village'] = app($villageModel)->find($this->village_id);
        }

        return $data;
    }

    public function validateGeographicRelationships(): array
    {
        $errors = [];

        // Validate province belongs to country
        if ($this->province_id && $this->country_id) {
            $provinceModel = $this->getGeographicModel('province');
            $province = app($provinceModel)->find($this->province_id);
            if ($province && $province->country_id != $this->country_id) {
                $errors['province_id'] = 'Selected province does not belong to the selected country';
            }
        }

        // Validate county belongs to province
        if ($this->county_id && $this->province_id) {
            $countyModel = $this->getGeographicModel('county');
            $county = app($countyModel)->find($this->county_id);
            if ($county && $county->province_id != $this->province_id) {
                $errors['county_id'] = 'Selected county does not belong to the selected province';
            }
        }

        // Validate city belongs to county
        if ($this->city_id && $this->county_id) {
            $cityModel = $this->getGeographicModel('city');
            $city = app($cityModel)->find($this->city_id);
            if ($city && $city->county_id != $this->county_id) {
                $errors['city_id'] = 'Selected city does not belong to the selected county';
            }
        }

        // Validate village belongs to city
        if ($this->village_id && $this->city_id) {
            $villageModel = $this->getGeographicModel('village');
            $village = app($villageModel)->find($this->village_id);
            if ($village && $village->city_id != $this->city_id) {
                $errors['village_id'] = 'Selected village does not belong to the selected city';
            }
        }

        return $errors;
    }

    public function setDefaultGeographicValues(): void
    {
        $defaults = [
            'country' => $this->getDefaultGeographicValue('country'),
            'province' => $this->getDefaultGeographicValue('province'),
            'county' => $this->getDefaultGeographicValue('county'),
            'city' => $this->getDefaultGeographicValue('city'),
            'village' => $this->getDefaultGeographicValue('village'),
        ];

        foreach ($defaults as $type => $defaultValue) {
            if ($defaultValue && !$this->{$type . '_id'}) {
                $model = $this->getGeographicModel($type);
                $entity = app($model)->where('name', $defaultValue)->first();
                if ($entity) {
                    $this->{$type . '_id'} = $entity->id;
                }
            }
        }
    }

    public function getGeographicPath(): Collection
    {
        $path = collect();

        if ($this->country_id) {
            $countryModel = $this->getGeographicModel('country');
            $path->push(app($countryModel)->find($this->country_id));
        }

        if ($this->province_id) {
            $provinceModel = $this->getGeographicModel('province');
            $path->push(app($provinceModel)->find($this->province_id));
        }

        if ($this->county_id) {
            $countyModel = $this->getGeographicModel('county');
            $path->push(app($countyModel)->find($this->county_id));
        }

        if ($this->city_id) {
            $cityModel = $this->getGeographicModel('city');
            $path->push(app($cityModel)->find($this->city_id));
        }

        if ($this->village_id) {
            $villageModel = $this->getGeographicModel('village');
            $path->push(app($villageModel)->find($this->village_id));
        }

        return $path;
    }

    public function getGeographicPathString(): string
    {
        return $this->getGeographicPath()
            ->pluck('name')
            ->filter()
            ->implode(' > ');
    }
}
