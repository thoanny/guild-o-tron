<?php namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('currency', [$this, 'formatCurrency'], ['is_safe' => ['html']]),
        ];
    }

    public function formatCurrency($number)
    {

      $copper = $number % 100;
      $silver = floor( ( $number % 10000 ) / 100 );
      $gold = floor( $number / 10000 );

      $po = '<span class="po">';
      $pa = '<span class="pa">';
      $pc = '<span class="pc">';

      if($gold) {
        $currency = $po . number_format($gold, 0, ',', ' ') . "</span>" . $pa . str_pad($silver, 2, '0', STR_PAD_LEFT) . "</span>" . $pc . str_pad($copper, 2, '0', STR_PAD_LEFT) . "</span>";
      }elseif($silver) {
        $currency = $pa . $silver . "</span>" . $pc . str_pad($copper, 2, '0', STR_PAD_LEFT) . "</span>";
      } elseif($copper) {
        $currency = $pc . $copper . "</span>";
      } else {
        $currency = $pc . $number . "</span>";
      }

      $currency = "<span class=\"currency\">{$currency}</span>";

      return $currency;
    }
}
