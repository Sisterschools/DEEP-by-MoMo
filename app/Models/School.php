<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class School extends Model
{
    use HasFactory;

    protected $perPage = 10;
    protected $fillable = [
        'title',
        'photo',
        'address',
        'country',
        'language',
        'description',
        'phone_number',
        'website',
        'country',
        'language',
        'founding_year',
        'student_capacity',
    ];

    protected $casts = [
        'founding_year' => 'integer',
        'student_capacity' => 'integer',
    ];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    // Many-to-Many Relationship with Teachers
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class);
    }

    // Many-to-Many Relationship with Students
    public function students()
    {
        return $this->belongsToMany(Student::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($school) {
            // Delete the associated user if exists
            $school->user->delete();

        });
    }

    // Scope a query to search for schools by title, address, or phone number.
    public function scopeSearch(Builder $query, $term)
    {
        return $query->where('title', 'like', "%{$term}%")
            ->orWhere('address', 'like', "%{$term}%")
            ->orWhere('phone_number', 'like', "%{$term}%");
    }

    public function allProjects()
    {
        return Project::where('school_id_1', $this->id)
            ->orWhere('school_id_2', $this->id);
    }
}