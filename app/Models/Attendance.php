<?php

namespace App\Models;

use App\Models\User;
use App\Models\AttendanceDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeCountAttendance($query, $status)
    {
        return $query->whereDate('created_at', Carbon::today())
            ->where('status', $status)
            ->count();
    }

    public function details()
    {
        return $this->hasMany(AttendanceDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
