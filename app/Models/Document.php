<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'status',
        'approved_at',
        'approved_by',
        'category',
        'visibility',
        'is_required',
        'uploaded_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'is_required' => 'boolean',
    ];

    /**
     * Get the user who uploaded the document.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who approved the document.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the uploader of the document.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the users who have viewed this document.
     */
    public function viewedBy()
    {
        return $this->belongsToMany(User::class, 'document_views')
            ->withPivot('viewed_at')
            ->withTimestamps();
    }

    /**
     * Check if the document is approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the document is pending approval.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the document is rejected.
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Approve the document.
     */
    public function approve($approverId)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approverId,
        ]);
    }

    /**
     * Reject the document.
     */
    public function reject($approverId)
    {
        $this->update([
            'status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => $approverId,
        ]);
    }
}
