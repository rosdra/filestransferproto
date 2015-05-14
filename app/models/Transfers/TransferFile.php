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

    protected  $appends = ['size_readable'];


    public function getSizeReadableAttribute(){
        $i = -1;
        $fileSizeInBytes = $this->attributes['size'];
        $byteUnits = [' KB', ' MB', ' GB', ' TB', 'PB', 'EB', 'ZB', 'YB'];
        do {
            $fileSizeInBytes = $fileSizeInBytes / 1024;
            $i++;
        } while ($fileSizeInBytes > 1024);

        return round(max($fileSizeInBytes, 0.1), 1) . $byteUnits[$i];
    }

    public function transfer(){
        $this->belongsTo('app\models\Transfers\Transfer', "transfer_id");
    }
}