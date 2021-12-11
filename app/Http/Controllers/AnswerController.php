<?php

namespace App\Http\Controllers;

use App\Models\AnswerGroup;
use App\Models\FormAnswer;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use DB;

class AnswerController extends Controller
{
    use GeneralTrait;
    public function submitAnswers(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$request->has('form_id') || !$request->has('answers')) {
                return $this->returnError('202', 'fail');
            }
            $answer_group_id = AnswerGroup::insertGetId([
                'user_id' => Auth()->user()->id ?? -1,
                'form_id' => $request->form_id
            ]);
            foreach ($request->answers as $answer) {
                if (is_array($answer['answer'])) {
                    foreach ($answer['answer'] as $choice) {
                        FormAnswer::create([
                            'answer_group' => $answer_group_id,
                            'form_question_id' => $answer['form_question_id'],
                            'answer' => $choice,
                            'multiple_answer' => 1
                        ]);
                    }
                } else {
                    FormAnswer::create([
                        'answer_group' => $answer_group_id,
                        'form_question_id' => $answer['form_question_id'],
                        'answer' => $answer['answer'],
                        'multiple_answer' => 0
                    ]);
                }
            }
            DB::commit();
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', $e->getMessage());
        }
    }
    public function getAnswers(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$request->has('form_id')) {
                return $this->returnError('202', 'fail');
            }
            // here there are three types
            DB::commit();
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', 'fail');
        }
    }

    public function deleteAnswers(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$request->has('form_id')) {
                return $this->returnError('202', 'fail');
            }
            $answers = FormAnswer::whereHas('question', function ($q) use ($request) {
                $q->where('form_id', $request->form_id);
                // ->where('user_id', Auth()->user()->id);
            })->delete();
            DB::commit();
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', $e->getMessage());
        }
    }
}
