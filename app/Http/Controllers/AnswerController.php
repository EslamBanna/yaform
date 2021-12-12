<?php

namespace App\Http\Controllers;

use App\Models\AnswerGroup;
use App\Models\Form;
use App\Models\FormAnswer;
use App\Models\MultipleAnswer;
use App\Models\Solution;
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
            $form = Form::find($request->form_id);
            if (!$form) {
                return $this->returnError('203', 'fail');
            }
            $answer_group_id = AnswerGroup::insertGetId([
                'user_id' => Auth()->user()->id ?? -1,
                'form_id' => $request->form_id
            ]);
            foreach ($request->answers as $answer) {
                if (is_array($answer['answer'])) {
                    $answer_id =  FormAnswer::insertGetId([
                        'answer_group' => $answer_group_id,
                        'form_question_id' => $answer['form_question_id'],
                        'answer' => 'lorem',
                        'multiple_answer' => 1
                    ]);
                    foreach ($answer['answer'] as $choice) {
                        MultipleAnswer::create([
                            'answer_id' => $answer_id,
                            'answer' => $choice,
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
            // here we shoud specify the return output
            if ($form->type == 2) {
                // calc score
                
                $right_solution = Solution::whereHas('question', function($q) use ($request){
                    $q->where('form_id', $request->form_id);
                })->get();
                return $right_solution;
                $user_core = 10;
                $form_socre = 100;
                return $this->returnSuccessMessage('Your score is ' . $user_core . ' from ' . $form_socre);
            } else {
                return $this->returnSuccessMessage($form->response_msg);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', $e->getMessage());
        }
    }
    public function getAnswers(Request $request)
    {
        try {
            if (!$request->has('filter')) {
                return $this->returnError('201', 'fail');
            }
            $answers = '';
            if ($request->filter == 0) {
                // all (a) question answers
                if (!$request->has('question_id')) {
                    return $this->returnError('202', 'fail');
                }
                $answers = FormAnswer::with(['multipleAnswerT' => function ($q) {
                    $q->select('id', 'answer', 'answer_id');
                }])
                    ->where('form_question_id', $request->question_id)
                    ->get();
            } else if ($request->filter == 1) {
                // get all answers of a user in a form
                if (!$request->has('form_id')) {
                    return $this->returnError('202', 'fail');
                }
                $answers = AnswerGroup::with(['answers.multipleAnswerT' => function ($q) {
                    $q->select('id', 'answer', 'answer_id');
                }])
                    ->where('form_id', $request->form_id)->paginate(1);
            } else if ($request->filter == 2) {
                //  summary
            }
            return $this->returnData('data', $answers);
        } catch (\Exception $e) {
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

            // group and multiple answer #################

            DB::commit();
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', $e->getMessage());
        }
    }
}
