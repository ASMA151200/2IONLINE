<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;

class QuestionController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Question::with('reponses')->orderBy('ordre')->get()
        ]);
    }

    public function store(StoreQuestionRequest $request)
    {
        $question = Question::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Question créée',
            'data' => $question
        ], 201);
    }

    public function show(Question $question)
    {
        return response()->json([
            'data' => $question->load('reponses')
        ]);
    }

    public function update(UpdateQuestionRequest $request, Question $question)
    {
        $question->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Question modifiée',
            'data' => $question
        ]);
    }

    public function destroy(Question $question)
    {
        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Question supprimée'
        ]);
    }
}