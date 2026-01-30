<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ChatbotTraining extends Model
{
    use HasFactory;
    protected $fillable = ['intent_name', 'training_text', 'source'];
}
