<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;

class UniversityService
{
    protected $baseUrl = 'http://universities.hipolabs.com/search';

    public function getUniversitiesByCountry($country)
    {
        $response = Http::get($this->baseUrl, [
            'country' => $country
        ]);

        return $response->json();
    }

    public function getAvailableCountries()
{
    return [
        'United States',
        'Canada',
        'United Kingdom',
        'Germany',
        'France',
        'Australia',
        'Egypt',
        'Jordan',
        'Japan'
    ];
}
}