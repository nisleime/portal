<?php

namespace App\Helpers;

use Carbon\Carbon;

class Format {

    public static function periodHtml($search)
    {
        $default_period_start = Carbon::now()->startOfMonth()->format('d/m/Y');
        $default_period_end = Carbon::now()->format('d/m/Y');

        if (is_null($search) || empty($search['first_date']) && empty($search['last_date'])) {
            return '<small class="text-black">' . $default_period_start . ' até ' . $default_period_end . '</small>';
        }

        return '<small class="text-black">' . Carbon::parse($search['first_date'])->format('d/m/Y') . ' até ' . Carbon::parse($search['last_date'])->format('d/m/Y') . '</small>';
    }
}
