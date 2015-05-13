<?php
namespace app\models\Transfers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Transfer extends \Eloquent {

    use SoftDeletingTrait;

    protected $table="transfers";

    protected $guarded = [
        "id", "created_at", "updated_at"
    ];
    protected $fillable = ['container_name', 'user_id', 'unique_id', 'sender_email', 'recipient_email', 'message'];

    public function getCreatedAtAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
    }

    public function files(){
        return $this->hasMany('app\models\Transfers\TransferFile', "transfer_id");
    }
}