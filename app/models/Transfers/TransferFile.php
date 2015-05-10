<?php
namespace app\models\Transfers;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class TransferFile extends \Eloquent {
    use SoftDeletingTrait;

    protected $table="transfer_file";

    protected $guarded = [
        "id", "created_at","updated_at"
    ];
    protected $fillable = ['original_name' ,'object_name','size', 'mimetype', 'slug', 'transfer_id'];


    public function transfer(){
        $this->belongsTo('app\models\Transfers\Transfer', "transfer_id");
    }
}