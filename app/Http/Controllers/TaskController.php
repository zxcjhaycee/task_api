<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use JWTAuth;

class TaskController extends Controller
{
    //
    protected $user;

    public function  __construct(){
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index(){

        $tasks = Task::with('tasks')->get(['title', 'description'])->toArray();

        return $tasks;
    }

    public function store(Request $request){

        $validate_data = $request->validate([
                'title' => 'required',
                'description' => 'required'
        ]);
        $validate_data['user_id'] = $request->user()->id;
        $result = $this->user->tasks()->create($validate_data);
        if($result){
            return response()->json([
                'success' => true,
                'task' => $validate_data
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Sorry, task could not be added.'
            ],500);
        }
           
    }

    public function show($id){
        $task = $this->user->tasks()->find($id);
        // $payload = JWTAuth::parseToken()->getPayload();
        // $expires_at = date('M d Y h:i A', $payload->get('exp')); 

        if(!$task){
            return response()->json([
                "success" => false,
                "message" => "Sorry, task with id ".$id." cannot be found"
            ],400);
        }
        return $task;
    }

    public function update(Request $request, $id){
        $validate_data = $request->validate([
            'title' => 'required',
            'description' => 'required'
        ]);
        $check_id = $this->user->tasks()->find($id);
        if(!$check_id){
            return response()->json([
                'success' => false,
                'message' => "Sorry, task with id ".$id." cannot be found"
            ],400);
        }
        $result = $this->user->tasks()->whereId($id)->update($validate_data);
        if($result){
            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully!'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => "Sorry, task could not be update"
            ],500);
        }
    }

    public function destroy($id){
        $check_id = $this->user->tasks()->find($id);
        if(!$check_id){
            return response()->json([
                'success' => false,
                'message' => "Sorry, task with id ".$id." cannot be found"
            ],400);
        }
        $result = $this->user->tasks()->whereId($id)->delete();
        if($result){
            return response()->json([
                'success' => true,
                'message' => "Task with id ".$id." was successfully deleted!"
            ]);
        }
    }
}
