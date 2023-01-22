<?php
 
 namespace App\Http\Services;
 use Illuminate\Support\Facades\Response;

 class DataService {

    public function getData($model){
        if(is_string($model)){
          $data = $model::get();
        }else{
          $data = $model->get();
        }
        return Response::json([
          'data'=>$data
        ]);
    }
    public function createData($model , $data){
      return $model::insert($data);
    }
    public function updateData($model , $data , $id){
      return $model::where('user_id',$id)->update($data);
    }
    public function viewData($model,$id){
      if(is_string($model)){
        return $model::find($id);
      }else{
        return $model->find($id);
      }
    }
  
 }

?>