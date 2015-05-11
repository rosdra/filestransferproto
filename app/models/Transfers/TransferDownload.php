<?php
namespace app\models\Transfers;

class TransferDownload extends \Eloquent {

    protected $table="transfer_downloads";

    protected $guarded = [
        "id", "created_at","updated_at"
    ];
    protected $fillable = ['user_id' ,'transfer_id'];
}