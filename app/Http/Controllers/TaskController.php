<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TaskRequest;
use Validator;
use App\Task;

class TaskController extends Controller
{

    /**
     * Create a new TaskController instance.
     *
     * @return void
     */
    public function __construct() {
        
    }

    /**
     * Display a listing of the task.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable',
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable'
        ]);

        if ($validator->fails()) {
            return $this->respondValidateError($validator->errors()->first());
        }

        // Get Total Hours of Task - Daily
        $dailyHours = DB::table('tasks')
                        ->select('tasks.user_id', 'tasks.date', DB::raw('SUM(tasks.hours) as total'))                        
                        ->groupBy(['user_id', 'date']);
        
        // Set Prefer as 0 if total hours > prefer hours
        $query = DB::table('tasks')
                    ->select(['tasks.*', DB::raw('users.name as user_name'), DB::raw('IF(daily_hours.total > users.prefer_work_hours, false, true) as prefer')])
                    ->leftJoin('users', 'tasks.user_id', '=', 'users.id')
                    ->joinSub($dailyHours, 'daily_hours', function ($join) {
                        $join->on('tasks.user_id', '=', 'daily_hours.user_id');
                        $join->on('tasks.date', '=', 'daily_hours.date');
                    });

        if ($request->user_id) {
            $query->where('tasks.user_id', '=', $request->user_id);
            $dailyHours->where('user_id', '=', $request->user_id);
        }
        if ($request->start_date) {
            $query->where('tasks.date', '>=', $request->start_date);
            $dailyHours->where('date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('tasks.date', '<=', $request->end_date);
            $dailyHours->where('date', '<=', $request->end_date);
        }

        $query->orderBy('date');

        $rows = $query->get();

        return $this->respondSuccess($rows);        
    }

    /**
     * Store new task.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TaskRequest $request){
		$task = Task::create($request->validated());

		return $this->respondSuccess($task);
    }

    /**
     * Update existing task.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TaskRequest $request, $id){
        $task = Task::find($id);
        if (!$task) {
            return $this->respondNotFoundError('Could not find the task');
        }

        $task->update($request->validated());

        return $this->respondSuccess($task);
    }

    /**
     * Remove the specified task.
     *
     * @return \Illuminate\Http\JsonResponse
     */
     public function delete($id) {
        $task = Task::find($id);
        if (!$task) {
            return $this->respondNotFoundError('Could not find the task');
        }
        
        if ($task->delete()){ // physical delete
            return $this->respondSuccess(['message' => 'Successfull']);
        } else {
            return $this->response->respondServerError('Could not delete task');
        }
    }
    
}
