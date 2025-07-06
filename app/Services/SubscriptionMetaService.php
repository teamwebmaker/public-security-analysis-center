<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SubscriptionMetaService
{
   public function detectBrowser(string $userAgent): string
   {
      return match (true) {
         str_contains($userAgent, 'OPR/') || str_contains($userAgent, 'Opera') => 'Opera',
         str_contains($userAgent, 'Edge') => 'Edge',
         str_contains($userAgent, 'Chrome') => 'Chrome',
         str_contains($userAgent, 'Safari') => 'Safari',
         str_contains($userAgent, 'Firefox') => 'Firefox',
         str_contains($userAgent, 'MSIE') || str_contains($userAgent, 'Trident/7') => 'Internet Explorer',
         default => 'Unknown',
      };
   }

   public function detectOS(string $userAgent): string
   {
      return match (true) {
         str_contains($userAgent, 'Windows') => 'Windows',
         str_contains($userAgent, 'Mac') => 'Mac',
         str_contains($userAgent, 'Linux') => 'Linux',
         str_contains($userAgent, 'Android') => 'Android',
         str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad') => 'iOS',
         default => 'Unknown',
      };
   }

   public function getLocation(string $ip): array
   {
      $response = Http::get("https://ipinfo.io/{$ip}/json");

      return $response->successful()
         ? ['country' => $response['country'] ?? null, 'city' => $response['city'] ?? null]
         : ['country' => null, 'city' => null];
   }
}
