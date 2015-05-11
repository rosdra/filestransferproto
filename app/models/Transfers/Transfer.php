<?php
namespace app\models\Transfers;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Transfer extends \Eloquent {

    use SoftDeletingTrait;

    protected $table="transfers";

    protected $guarded = [
        "id", "created_at","updated_at"
    ];
    protected $fillable = ['container_name' ,'user_id'];


    public function files(){
        return $this->hasMany('app\models\Transfers\TransferFile', "transfer_id");
    }
}