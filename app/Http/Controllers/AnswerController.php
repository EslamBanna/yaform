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
            $socre = 0;
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
                    $user_answer = [];
                    foreach ($answer['answer'] as $index => $choice) {
                        MultipleAnswer::create([
                            'answer_id' => $answer_id,
                            'answer' => $choice,
                        ]);
                        $user_answer[$index] = $choice;
                    }
                    if ($form->type == 2) {
                        // check every answer
                        $right_solution = Solution::select('solution')->where('question_id', $answer['form_question_id'])->get();
                        $final_right_solution = [];
                        foreach ($right_solution as $index => $final_solution) {
                            $final_right_solution[$index] = $final_solution['solution'];
                        }
                        $containsAllValues = !array_diff($user_answer, $final_right_solution);
                        if ($containsAllValues) {
                            $socre++;
                        }
                    }
                } else {
                    FormAnswer::create([
                        'answer_group' => $answer_group_id,
                        'form_question_id' => $answer['form_question_id'],
                        'answer' => $answer['answer'],
                        'multiple_answer' => 0
                    ]);
                    if ($form->type == 2) {
                        // check one answer
                        $right_solution = Solution::where('question_id', $answer['form_question_id'])->first();
                        if ($right_solution->solution == $answer['answer']) {
                            $socre++;
                        }
                    }
                }
            }
            DB::commit();
            // here we shoud specify the return output
            if ($form->type == 2) {
                $question_count = count($form->onlyFormQuestions);
                return $this->returnSuccessMessage('Your score is ' . $socre . ' from ' . $question_count);
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
