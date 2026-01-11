<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'logo',
        'favicon_16',
        'favicon_32',
        'description',
        'email',
        'phone',
        'address',
        'website_url',
        'gst_number',
    ];
}
