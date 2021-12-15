<?php

namespace Opengis\LogVisits\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getTable()
    {
        return config('log-visits.table-name', 'page_visits_log');
    }
}
