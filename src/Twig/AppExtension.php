<?php namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Symfony\Component\HttpFoundation\Request;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('currency', [$this, 'formatCurrency'], ['is_safe' => ['html']]),
            new TwigFilter('anonymize', [$this, 'anonymizeAccountName']),
            new TwigFilter('md5', [$this, 'stringToMd5']),
            new TwigFilter('transraids', [$this, 'translateRaidsName'], ['needs_context' => true]),
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

    public function anonymizeAccountName($name) {
      $name = explode('.', $name);
      $name = $name[0];

      return ucfirst($name);
    }

    public function stringToMd5($string) {
      return md5($string);
    }

    public function translateRaidsName($context, $string)
    {
      $locale = $context['app']->getRequest()->getLocale();
      $locales = ['fr', 'en', 'es', 'de'];

      if(!in_array($locale, $locales)) {
        $locale = 'en';
      }

      $raids = [
        'en' => [
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
        ],
        'fr' => [
          'forsaken_thicket' => 'Taillis abandonné',
          'spirit_vale' => 'Vallée des esprits',
          'vale_guardian' => 'Gardien de la vallée',
          'gorseval' => 'Gorseval le Disparate',
          'sabetha' => 'Sabetha la saboteuse',
          'salvation_pass' => 'Passage de la rédemption',
          'slothasor' => 'Paressor',
          'bandit_trio' => 'Trio de bandits',
          'matthias' => 'Matthias Gabrel',
          'stronghold_of_the_faithful' => 'Forteresse des Fidèles',
          'escort' => 'Assaut de la forteresse',
          'keep_construct' => 'Titan du fort',
          'xera' => 'Xera',
          'bastion_of_the_penitent' => 'Bastion du pénitent',
          'cairn' => 'Cairn l\'Indomptable',
          'mursaat_overseer' => 'Surveillant mursaat',
          'samarog' => 'Samarog',
          'deimos' => 'Deimos',
          'hall_of_chains' => 'Salle des chaînes',
          'soulless_horror' => 'Horreur sans âme',
          'river_of_souls' => 'Rivière des âmes',
          'statues_of_grenth' => 'Statues de Grenth',
          'voice_in_the_void' => 'Dhuum',
          'mythwright_gambit' => 'Gambit de forgeconte',
          'conjured_amalgamate' => 'Amalgame conjuré',
          'twin_largos' => 'Jumeaux largos',
          'qadim' => 'Qadim',
          'the_key_of_ahdashim' => 'La clé d\'Ahdashim',
          'adina' => 'Cardinale Adina',
          'sabir' => 'Cardinal Sabir',
          'qadim_the_peerless' => 'Qadim l\'Inégalé',
        ],
        'es' => [
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
        ],
        'de' => [
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
          '' => '',
        ],
      ];

      return (isset($raids[$locale][$string]) && !empty($raids[$locale][$string])) ? $raids[$locale][$string] : $string;
    }
}
