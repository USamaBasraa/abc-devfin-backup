<?php

namespace App\Services\StudentRecord\ChallanPosting;

/**
 * Picks the poster for a challan category.
 *
 * One class per category, each replacing one branch of the old procedures, so a rule
 * change is confined to a single file.
 */
class ChallanPosterFactory
{
    /** category => poster class */
    private static $map = [
        'Full_semester'            => FullSemesterPoster::class,
        'Short/Summer_Course'      => ShortSummerPoster::class,
        'Credit_hours'             => CreditHourPoster::class,
        'additional_Full_semester' => AdditionalCreditHourPoster::class,
        'Hostel_Fee'               => HostelPoster::class,
    ];

    /** @return AbstractChallanPoster|null */
    public function make($category)
    {
        $category = trim((string) $category);
        if (!isset(self::$map[$category])) {
            return null;
        }

        $class = self::$map[$category];

        return new $class();
    }

    public function supports($category)
    {
        return isset(self::$map[trim((string) $category)]);
    }

    public static function categories()
    {
        return array_keys(self::$map);
    }
}
