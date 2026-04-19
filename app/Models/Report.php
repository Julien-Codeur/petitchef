<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'reported_user_id',
        'reported_dish_id',
        'reported_order_id',
        'type',
        'category',
        'description',
        'status',
        'admin_comment',
        'priority',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relations
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function reportedDish(): BelongsTo
    {
        return $this->belongsTo(Dish::class, 'reported_dish_id');
    }

    public function reportedOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'reported_order_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInvestigating($query)
    {
        return $query->where('status', 'investigating');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['pending', 'investigating']);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>=', 3);
    }

    public function scopeByReporter($query, $userId)
    {
        return $query->where('reporter_id', $userId);
    }

    public function scopeAboutUser($query, $userId)
    {
        return $query->where('reported_user_id', $userId);
    }

    /**
     * Helper methods
     */
    public function getStatusBadge()
    {
        return match ($this->status) {
            'pending' => '⏳ En attente',
            'investigating' => '🔍 En investigation',
            'resolved' => '✅ Résolu',
            'rejected' => '❌ Rejeté',
            'action_taken' => '⚠️ Action prise',
            default => '❓ Inconnu',
        };
    }

    public function getCategoryLabel()
    {
        return match ($this->category) {
            'quality' => 'Qualité insuffisante',
            'hygiene' => 'Hygiène douteuse',
            'behavior' => 'Comportement inapproprié',
            'abusive' => 'Langage abusif',
            'fraud' => 'Fraude',
            'late' => 'Livraison tardive',
            'quality_issue' => 'Problème de qualité',
            'missing_items' => 'Éléments manquants',
            'expired' => 'Produit expiré',
            'other' => 'Autre',
            default => 'Non spécifié',
        };
    }

    public function getPriorityLabel()
    {
        return match ($this->priority) {
            1 => '🟢 Basse',
            2 => '🟡 Moyenne',
            3 => '🔴 Haute',
            4 => '🔴 Critique',
            default => '❓ Inconnue',
        };
    }

    public function getPriorityColor()
    {
        return match ($this->priority) {
            1 => '#28a745', // green
            2 => '#ffc107', // yellow
            3 => '#fd7e14', // orange
            4 => '#dc3545', // red
            default => '#6c757d', // gray
        };
    }

    public function markAsInvestigating(): void
    {
        $this->update(['status' => 'investigating']);
    }

    public function markAsResolved($adminId, $comment = null): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => $adminId,
            'admin_comment' => $comment,
        ]);
    }

    public function markAsRejected($adminId, $comment = null): void
    {
        $this->update([
            'status' => 'rejected',
            'resolved_at' => now(),
            'resolved_by' => $adminId,
            'admin_comment' => $comment,
        ]);
    }

    public function takeAction($adminId, $comment = null): void
    {
        $this->update([
            'status' => 'action_taken',
            'resolved_at' => now(),
            'resolved_by' => $adminId,
            'admin_comment' => $comment,
        ]);
    }
}
