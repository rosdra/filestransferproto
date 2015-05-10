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
        $transfer = Transfer::find($id)->with("files");
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

                $arrayFilesToSave[] = $file;
            }
            $u->files()->saveMany($arrayFilesToSave);
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