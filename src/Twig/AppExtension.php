<?php

namespace App\Twig;

use App\Entity\LikeNotification;
use Symfony\Component\Finder\Glob;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigTest;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var string
     */
    private $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'priceFilter'])
        ];
    }

    public function getGlobals(): array
    {
       return [
           'locale' => $this->locale
       ];
    }

    public function priceFilter($amount)
    {
        return '$'. number_format($amount, 2, ',', ' ');
    }

    public function getTests()
    {
        return [
            new TwigTest('like', function ($obj) {
                return $obj instanceof LikeNotification;
            })
        ];
    }
}