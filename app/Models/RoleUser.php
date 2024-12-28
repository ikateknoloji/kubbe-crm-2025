<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    /**
     * Tablo adı
     *
     * @var string
     */
    protected $table = 'role_users';

    /**
     * Doldurulabilir alanlar
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'role_id',
    ];

    /**
     * RoleUser ile User arasındaki ilişki.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * RoleUser ile Role arasındaki ilişki.
     *
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
