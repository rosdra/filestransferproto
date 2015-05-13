<?php namespace app\Lib\Storage\Transfers;
 
interface ITransferRepository
{

    public function all();

    public function find($id);
    public function findByUniqueId($unique_id);

    public function create($input);

    public function addNewFile($id,$input);

    public function update($id, $input);

    public function delete($id);
}