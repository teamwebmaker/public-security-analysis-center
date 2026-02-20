<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;

class SenderGeClient
{
   public function __construct(
      private ?string $baseUrl = null,
      private ?string $apiKey = null,
      private int $timeout = 15,
   ) {
      $this->baseUrl = $this->baseUrl ?? config('services.senderge.base_url');
      $this->apiKey = $this->apiKey ?? config('services.senderge.apikey');
      $this->timeout = $this->timeout ?? config('services.senderge.timeout', 15);
   }

   /**
    * Send SMS via POST.
    *
    * @param  int    $smsno 1=advertising, 2=information
    * @param  string $destination Georgian mobile (5xxxxxxxx), +995 optional
    * @param  string $content unicode allowed
    */
   public function sendSms(int $smsno, string $destination, string $content): array
   {
      $this->assertConfigured();
      $destination = $this->normalizeDestination($destination);
      $this->validateSmsno($smsno);
      $this->validateContent($content);

      // Sender.Ge examples use form fields; we do the same.
      $response = $this->http()
         ->withHeaders(['apikey' => $this->apiKey])
         ->asForm()
         ->post($this->url('/send.php'), [
               'apikey' => $this->apiKey,
               'smsno' => $smsno,
               'destination' => $destination,
               'content' => $content,
            ]);

      return $this->parseResponse($response->status(), $response->body(), 'sendSms');
   }

   public function getBalance(): array
   {
      $this->assertConfigured();

      $response = $this->http()
         ->asForm()
         ->post($this->url('/getBalance.php'), [
               'apikey' => $this->apiKey,
            ]);

      return $this->parseResponse($response->status(), $response->body(), 'getBalance');
   }

   public function getDeliveryReport(string $messageId): array
   {
      $this->assertConfigured();

      if ($messageId === '') {
         throw new \InvalidArgumentException('messageId is required.');
      }

      $response = $this->http()
         ->withHeaders(['apikey' => $this->apiKey])
         ->asForm()
         ->post($this->url('/callback.php'), [
               'apikey' => $this->apiKey,
               'messageId' => $messageId,
            ]);

      return $this->parseResponse($response->status(), $response->body(), 'getDeliveryReport');
   }


   // =====================
   // Helpers  
   // =====================

   private function http()
   {
      return Http::timeout($this->timeout)
         ->retry(2, 300, throw: false);
   }

   private function url(string $path): string
   {
      return rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
   }

   private function assertConfigured(): void
   {
      if (!$this->apiKey) {
         throw new \RuntimeException('Sender.Ge API key is not configured (services.senderge.apikey).');
      }
      if (!$this->baseUrl) {
         throw new \RuntimeException('Sender.Ge base URL is not configured (services.senderge.base_url).');
      }
   }

   private function validateDestination(string $destination): void
   {
      if (!preg_match('/^5\d{8}$/', $destination)) {
         throw new \InvalidArgumentException('destination must be a Georgian mobile (5xxxxxxxx).');
      }
   }

   private function normalizeDestination(string $destination): string
   {
      $cleaned = preg_replace('/^(\+?995)/', '', $destination);
      $cleaned = $cleaned ?? $destination;
      $cleaned = trim($cleaned);
      $this->validateDestination($cleaned);

      return $cleaned;
   }

   private function validateContent(string $content): void
   {
      if ($content === '') {
         throw new \InvalidArgumentException('content is required.');
      }
      if (mb_strlen($content) > 1000) {
         throw new \InvalidArgumentException('content is too long (max 1000 characters).');
      }
   }

   private function validateSmsno(int $smsno): void
   {
      if (!in_array($smsno, [1, 2], true)) {
         throw new \InvalidArgumentException('smsno must be 1 (advertising) or 2 (information).');
      }
   }

   /**
    * This parser tries JSON first, then falls back to key=value parsing.
    */
   private function parseResponse(int $status, string $body, string $op): array
   {
      $bodyTrim = trim($body);

      // Try JSON
      $json = json_decode($bodyTrim, true);
      if (is_array($json)) {
         return [
            'ok' => ($status >= 200 && $status < 300),
            'http_status' => $status,
            'data' => $json,
            'raw' => $bodyTrim,
            'operation' => $op,
         ];
      }

      // Try "key=value&key2=value2"
      parse_str($bodyTrim, $parsed);
      if (is_array($parsed) && count($parsed) > 0) {
         return [
            'ok' => ($status >= 200 && $status < 300),
            'http_status' => $status,
            'data' => $parsed,
            'raw' => $bodyTrim,
            'operation' => $op,
         ];
      }

      // Fallback
      return [
         'ok' => ($status >= 200 && $status < 300),
         'http_status' => $status,
         'data' => [],
         'raw' => $bodyTrim,
         'operation' => $op,
      ];
   }
}
