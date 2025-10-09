<?php

namespace App\Services;

use App\Models\Option;

class SettingService
{
    public function getPerPage()
    {
        return Option::where('reference', 'pagination')->value('value') ?? 15;
    }
}
