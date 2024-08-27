<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Todo;

class TodoController extends Controller
{
    public function index()
    {
        return view('welcome',['todos' => Todo::orderBy('id','DESC')->get()]);
    }
    // --------------------------------------------------------------

    public function showAll()
    {
        $todos = Todo::orderBy('id','DESC')->get();
        return response()->json($todos);
    }
    // --------------------------------------------------------------

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'unique:todos'],
        ]);  
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }
        
        $todo = Todo::updateOrCreate(
            ['id'=>$request->id],
            ['name'=>$request->name]
        );

        return response()->json($todo);
    }
    // --------------------------------------------------------------

    public function update(Todo $todo)
    {
        $todo = Todo::where('id',$todo->id)->first();

        if(!is_null($todo)){
            Todo::where('id',$todo->id)->update(['status' => "Completed"]);

            $data = array('msg' => 'Updated successfully !! ', 'success' => true);
            return json_encode($data);

        }else{
            $data = array('msg' => 'Todo Not Found !! ', 'error' => true);
            return json_encode($data);
        }
    }
    // --------------------------------------------------------------

    public function destroy(Todo $todo)
    {
        $todo->delete();
        return response()->json('success');
    }
}
