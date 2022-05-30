<?php
    
    namespace App\Models;
    
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    
    class BaseModel extends Model
    {
        use HasFactory;
        
        protected const CACHE_TIMEOUT = 3600;
        
        protected static function tableName(): string
        {
            return with(new static)->getTable();
        }
        
    }
