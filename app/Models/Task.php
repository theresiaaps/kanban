<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    public const STATUS_NOT_STARTED ='not_started'; 
    public const STATUS_IN_PROGRESS ="in_progress";
    public const STATUS_COMPLETED="completed"; 
    public const STATUS_IN_REVIEW="in_review";
    protected $fillable = ['name', 'detail', 'due_date', 'status'];
}
