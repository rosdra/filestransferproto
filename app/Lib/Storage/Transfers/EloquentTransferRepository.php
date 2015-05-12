<?php namespace app\Lib\Storage\Transfers;
 
use app\models\Transfers\Transfer;
use app\models\Transfers\TransferFile;
 
class EloquentTransferRepository implements ITransferRepository
{

    public function all()
    {
        return Transfer::all();
    }

    public function find($id)
    {
        $transfer = Transfer::find($id);
        return $transfer;
    }

    public function findByUniqueId($unique_id)
    {
        $transfer = Transfer::where("unique_id, ",$unique_id)->first();
        return $transfer;
    }

    /*
     * creates a new transfer object, it receive an array with container information and all files uploaded
     *
     * */
    public function create($input)
    {
        $u = new Transfer;

        $u->container_name = $input["container_name"];
        $u->unique_id = uniqid("",true);
        if(\Auth::check()) {
            $u->user_id = \Auth::user()->id;
        }

        $u->save();

        if(array_key_exists("files", $input)){
            $arrayFilesToSave = array();
            foreach($input["files"] as $f){
                $file  = new TransferFile;
                $file->original_name = $f["original_name"];
                $file->object_name = $f["object_name"];
                $file->size = $f["size"];
                $file->mimetype = $f["mimetype"];
                $file->slug = $f["slug"];
                $file->transfer_id = $u->id;

                $file->save();
                //$arrayFilesToSave[] = $file;
            }
            //$u->files()->saveMany($arrayFilesToSave);
        }

        \Session::set('transfer_id',$u->id);

        return $u;
    }

    public function addNewFile($id,$f){
        $u = $this->find($id);

        if($f != null) {
            $file = new TransferFile;
            $file->original_name = $f["original_name"];
            $file->object_name = $f["object_name"];
            $file->size = $f["size"];
            $file->mimetype = $f["mimetype"];
            $file->slug = $f["slug"];
            $file->transfer_id = $u->id;

            $file->save();
        }

        return $u;
    }

    public function update($id, $input)
    {
        //
    }

    public function delete($id)
    {

        $u = $this->find($id);

        foreach ($u->files as $f){
            $f->delete();
        }

        return $u->delete();
    }
}