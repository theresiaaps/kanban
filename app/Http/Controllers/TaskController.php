<?php

namespace App\Http\Controllers;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{

  public function edit($id)
  {
      $pageTitle = 'Edit Task';
      $task = Task::find($id);

      return view('tasks.edit', ['pageTitle' => $pageTitle, 'task' => $task]);
  }

  public function create()
  {
      $pageTitle = 'Create Task';
      return view('tasks.create')->with ('pageTitle', $pageTitle);
    //   return view('tasks.create', ['pageTitle' => $pageTitle]);
  }

  public function index()
{
    $pageTitle = 'Task List'; // Ditambahkan
    $tasks = Task::all();
    return view('tasks.index', [
        'pageTitle' => $pageTitle, //Ditambahkan
        'tasks' => $tasks,
    ]);
}
  public function store(Request $request){
    $request->validate(
      [
          'name' => 'required',
          'due_date' => 'required',
          'status' => 'required',
      ],
      $request->all()
  );
    Task::create([
      'name' => $request->name,
      'detail' => $request->detail,
      'due_date' => $request->due_date,
      'status' => $request->status,
  ]);

  return redirect()->route('tasks.index');
  }
  public function update(Request $request, $id){
    $task = Task::find($id);
    $task->update([
      'name' => $request->name,
      'detail' => $request->detail,
      'due_date' => $request->due_date,
      'status' => $request->status,
  ]);
  return redirect()->route('tasks.index');
  }
  public function delete($id){
    $task = Task::find($id);
    return view('tasks.delete',["pageTitle"=>"halaman hapus", 'task'=>$task]);
  }
  public function destroy($id){
    $task = Task::find($id);
    $task -> delete();
    return redirect()->route('tasks.index');
  }
  public function progress(){
    $semuaTasks = Task::all();
    $taskdifilter = $semuaTasks->groupBy('status');
    $tasks=[
    Task::STATUS_NOT_STARTED=>$taskdifilter->get(Task::STATUS_NOT_STARTED,[]), 
    Task::STATUS_IN_PROGRESS=>$taskdifilter->get(Task::STATUS_IN_PROGRESS,[]), 
    Task::STATUS_COMPLETED=>$taskdifilter->get(Task::STATUS_COMPLETED,[]), 
    Task::STATUS_IN_REVIEW=>$taskdifilter->get(Task::STATUS_IN_REVIEW,[])];
    $pageTitle = 'Task Progress';
    //var_dump($tasks);
    return view('tasks.progress',["pageTitle"=>$pageTitle, 'tasks'=>$tasks]);
  }
  public function move(int $id, Request $request)
{
    $task = Task::findOrFail($id);

    $task->update([
        'status' => $request->status,
    ]);

    return redirect()->route('tasks.progress');
}
}