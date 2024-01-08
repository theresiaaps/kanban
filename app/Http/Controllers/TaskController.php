<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{  
    public function __construct()
    {
    }

    public function home()
    {
        $tasks = Task::where('user_id', auth()->id())->get();

        $completed_count = $tasks
            ->where('status', Task::STATUS_COMPLETED)
            ->count();

        $uncompleted_count = $tasks
            ->whereNotIn('status', Task::STATUS_COMPLETED)
            ->count();

        return view('home', [
            'completed_count' => $completed_count,
            'uncompleted_count' => $uncompleted_count,
        ]);
    }

    public function index()
    {
        $pageTitle = 'Task List';

        if (Gate::allows('viewAnyTask', Task::class)) {
            $tasks = Task::all();
        } else {
            $tasks = Task::where('user_id', Auth::user()->id)->get();
        }
              
        return view('tasks.index', [
            'pageTitle' => $pageTitle,
            'tasks' => $tasks,
        ]);
    }

    public function create($status = NULL)
    {
        $pageTitle = 'Create Task';

        return view('tasks.create', ['pageTitle' => $pageTitle, 'status' => $status]);
    }

    public function store(Request $request)
    {
        // Code untuk proses validasi
        $request->validate(
            [
                'name' => 'required',
                'due_date' => 'required',
                'status' => 'required',
                'file' => ['max:5000', 'mimes:pdf,jpeg,png'],
            ],            
    // Tambahkan pesan error
            [
                'file.max' => 'The file size exceed 5 mb',
                'file.mimes' => 'Must be a file of type: pdf,jpeg,png',
            ],
            $request->all()
        );
        

        // Tambahkan database transaction
        DB::beginTransaction();
        try {
           $task = Task::create([
                'name' => $request->name,
                'detail' => $request->detail,
                'due_date' => $request->due_date,
                'status' => $request->status,
                'user_id' => Auth::user()->id,
                'file' => $request->file
            ]);

            $file = $request->file('file');
            if ($file) {
                $filename = $file->getClientOriginalName();
                $path = $file->storePubliclyAs(
                    'tasks',
                    $file->hashName(),
                    'public'
                );

                TaskFile::create([
                    'task_id' => $task->id,
                    'filename' => $filename,
                    'path' => $path,
                ]);
            }

            DB::commit();

            return redirect()->route('tasks.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
                ->route('tasks.create')
                ->with('error', $th->getMessage());
        }

        return redirect()->route('tasks.index');
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Task';
        $task = Task::find($id); // Diperbarui      

        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }

        return view('tasks.edit', ['pageTitle' => $pageTitle, 'task' => $task]);
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }

        $task->update([
            // data task yang berasal dari formulir
            'name' => $request->name,
            'detail' => $request->detail,
            'due_date' => $request->duedate,
            'status' => $request->status
        ]);

        // Code untuk melakukan redirect menuju GET /tasks
        return redirect()->route('tasks.index');
    }

    public function delete($id)
    {
        // Menyebutkan judul dari halaman yaitu "Delete Task"   
        $pageTitle = 'Delete Task'; 
        //  Memperoleh data task menggunakan $id
        $task = Task::find($id);   
        
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('deleteAnyTask', Task::class);
        }
        // Menghasilkan nilai return berupa file view dengan halaman dan data task di atas 
        return view('tasks.delete', ['pageTitle' => $pageTitle, 'task' => $task]);
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        $task->delete();

        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('deleteAnyTask', Task::class);
        }
        // Melakukan redirect menuju tasks.index
        return redirect()->route('tasks.index');
    }   

    public function progress()
    {
        $title = 'Task Progress';

        if (Gate::allows('viewAnyTask', Task::class)) {
            $tasks = Task::all();
        } else {
            $tasks = Task::where('user_id', Auth::user()->id)->get();
        }        

        $filteredTasks = $tasks->groupBy('status');        

        $tasks = [
            Task::STATUS_NOT_STARTED => $filteredTasks->get(
                Task::STATUS_NOT_STARTED, []
            ),
            Task::STATUS_IN_PROGRESS => $filteredTasks->get(
                Task::STATUS_IN_PROGRESS, []
            ),
            Task::STATUS_IN_REVIEW => $filteredTasks->get(
                Task::STATUS_IN_REVIEW, []
            ),
            Task::STATUS_COMPLETED => $filteredTasks->get(
                Task::STATUS_COMPLETED, []
            ),
        ];

        return view('tasks.progress', [
            'pageTitle' => $title,
            'tasks' => $tasks,
        ]);
    }

    public function move(int $id, Request $request)
    {
        $task = Task::findOrFail($id);

        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }

        $task->update([
            'status' => $request->status,
        ]);

        return redirect()->route('tasks.progress');
    }

    public function cardComplete(Request $request, $id)
    {
        $task = Task::find($id);

        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }

        $task->update([
            'status' => 'completed'
        ]);

        return redirect()->route('tasks.progress');
    }

    public function listComplete(Request $request, $id)
    {
        $task = Task::find($id);

        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }

        $task->update([
            'status' => 'completed'
        ]);

        return redirect()->route('tasks.index');
    }
}