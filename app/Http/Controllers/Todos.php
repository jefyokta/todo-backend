<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Schema\BaseJsonSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Todos extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $todos = Todo::where('user_id', $request->user->id)
            ->latest()
            ->paginate(5);

        return response()->json(
            (new BaseJsonSchema(data: $todos))->toArray()
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $data = Validator::make($request->all(), [
                'title' => 'required|string|max:100',
                'descriptions' => 'required|string',
                'is_done' => 'boolean'
            ])->validate();
        } catch (ValidationException $e) {

            return response()->json(
                (new BaseJsonSchema(
                    false,
                    message: 'Validation failed',
                    errors: $e->errors()
                ))->toArray(),
                422
            );
        }

        $todo = Todo::create([
            ...$data,
            'user_id' => $request->user->id
        ]);

        return response()->json(
            (new BaseJsonSchema(
                message: 'Todo created',
                data: $todo
            ))->toArray(),
            201
        );
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, Todo $todo)
    {
        if ($todo->user_id !== $request->user->id) {
            return response()->json(
                (new BaseJsonSchema(false, message: 'Forbidden'))->toArray(),
                403
            );
        }

        return response()->json(
            (new BaseJsonSchema(data: $todo))->toArray()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        if ($todo->user_id !== $request->user->id) {
            return response()->json(
                (new BaseJsonSchema(false, message: 'Forbidden'))->toArray(),
                403
            );
        }

        try {

            $data = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:100',
                'descriptions' => 'sometimes|string',
                'is_done' => 'boolean'
            ])->validate();
        } catch (ValidationException $e) {

            return response()->json(
                (new BaseJsonSchema(
                    false,
                    message: 'Validation failed',
                    errors: $e->errors()
                ))->toArray(),
                422
            );
        }

        $todo->update($data);

        return response()->json(
            (new BaseJsonSchema(
                message: 'Todo updated',
                data: $todo
            ))->toArray()
        );
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Todo $todo)
    {
        if ($todo->user_id !== $request->user->id) {
            return response()->json(
                (new BaseJsonSchema(false, message: 'Forbidden'))->toArray(),
                403
            );
        }

        $todo->delete();

        return response()->json(
            (new BaseJsonSchema(message: 'Todo deleted'))->toArray()
        );
    }
}
