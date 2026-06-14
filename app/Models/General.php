<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class General extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'rif',
        'department',
        'title_report_1',
        'subtitle_report_1',
        'title_report_2',
        'subtitle_report_2',
        'title_report_3',
        'subtitle_report_3',
        'title_report_4',
        'subtitle_report_4',
        'footer',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
