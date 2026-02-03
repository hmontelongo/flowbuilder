<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flow extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'nodes', 'edges'];

    /**
     * Default attribute values (MySQL doesn't support JSON column defaults).
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'nodes' => '[]',
        'edges' => '[]',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nodes' => 'array',
            'edges' => 'array',
        ];
    }
}
