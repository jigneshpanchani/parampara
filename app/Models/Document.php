<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory;

    use SoftDeletes;

    use ActivityTrait;

    protected $table = 'documents';

    protected static $logName = 'Attachment';

    public function getLogDescription(string $event): string
    {
        return "Attachment <strong>{$this->orignal_name}</strong>  has been {$event} by";
    }

    protected static $logAttributes = [
        'entity_id',
        'entity_type',
        'name',
        'orignal_name',
        'created_by',
        'updated_by'
    ];

    protected $fillable = [
        'entity_id',
        'entity_type',
        'name',
        'orignal_name',
        'created_by',
        'updated_by'
    ];
}
