<?php

namespace App\Models;

use App\Enums\InviteStatus;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $email
 * @property string $token
 * @property int $invited_by
 * @property UserRole $role
 * @property InviteStatus $status
 * @property string $expires_at
 * @property string|null $accepted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $invitedBy
 * @method static \Database\Factories\InvitationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereInvitedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
