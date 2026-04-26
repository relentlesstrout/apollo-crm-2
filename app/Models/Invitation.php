<?php

namespace App\Models;

use App\Enums\InviteStatus;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    use HasFactory;
    protected $casts = [
        'role' => UserRole::class,
        'status' => InviteStatus::class,
    ];
    protected $fillable = ['email', 'token', 'invited_by', 'role', 'accepted_at', 'status', 'expires_at'];


    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isPending(): bool
    {
        return $this->accepted_at === null;
    }
}
