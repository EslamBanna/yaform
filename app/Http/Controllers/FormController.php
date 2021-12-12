<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormQuestion;
use App\Models\MultipleChoiceQ;
use App\Models\Solution;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Validator;
use DB;

class FormController extends Controller
{
    use GeneralTrait;
    public function createForm(Request $request)
    {
        // return 55;
        DB::beginTransaction();;
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'form_type' => 'required|integer|min:0|max:3',
                // 'q_type' => 'required|integer|min:0|max:10',
            ]);
            if ($validator->fails()) {
                return $this->returnError('202', 'fail');
            }
            $logo = '';
            if ($request->hasFile('logo')) {
                $logo  = $this->saveImage($request->logo, 'forms_logo');
            }
            $form_id = Form::insertGetId([
                'user_id' => Auth()->user()->id,
                'title' => $request->title,
                'description' => $request->description,
                'logo' => $logo,
                'type' => $request->form_type,
                'facebook_link' => $request->facebook_link,
                'twitter_link' => $request->twitter_link,
                'instgram_link' => $request->instgram_link
            ]);

            if (isset($request->questions) && count($request->questions) > 0) {
                foreach ($request->questions as $question) {
                    if ($question['q_type'] == 2 || $question['q_type'] == 3 || $question['q_type'] == 4) {
                        $question_id =  FormQuestion::insertGetId([
                            'form_id' => $form_id,
                            'title' => $question['title'],
                            'type' => $question['q_type'],
                            'required' => $question['required'] ?? 0
                        ]);
                        if ($question['q_type'] == 2) {
                            foreach ($question['solution'] as $solution) {
                                Solution::create([
                                    'question_id' => $question_id,
                                    'solution' => $solution
                                ]);
                            }
                        } else {
                            Solution::create([
                                'question_id' => $question_id,
                                'solution' => $question['solution']
                            ]);
                        }
                        foreach ($question['choices'] as $choice) {
                            MultipleChoiceQ::create([
                                'question_id' => $question_id,
                                'choice' => $choice
                            ]);
                        }
                    } else if ($question['q_type'] == 8) {
                        // youtub_link
                        FormQuestion::create([
                            'form_id' => $form_id,
                            'title' => $question['title'],
                            'description' => $question['youtube_link'],
                            'type' => $question['q_type'],
                        ]);
                    } else if ($question['q_type'] == 9) {
                        // photo
                        $photo = '';
                        // $photo  = $this->saveImage($question['photo'], 'forms_questions');
                        FormQuestion::create([
                            'form_id' => $form_id,
                            'title' => $question['title'],
                            'description' => $photo,
                            'type' => $question['q_type'],
                        ]);
                    } else if ($question['q_type'] == 10) {
                        // head
                        FormQuestion::create([
                            'form_id' => $form_id,
                            'title' => $question['title'],
                            'description' => $question['description'],
                            'type' => $question['q_type'],
                        ]);
                    } else {
                        $question_id =  FormQuestion::insertGetId([
                            'form_id' => $form_id,
                            'title' => $question['title'],
                            // 'description' => $question->description,
                            'type' => $question['q_type'],
                            'required' => $question['required'] ?? 0
                        ]);
                        Solution::create([
                            'question_id' => $question_id,
                            'solution' => $question['solution']
                        ]);
                    }
                }
            }
            DB::commit();
            // return 44;
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', $e->getMessage());
        }
    }
    public function getForms()
    {
        try {
            $forms = Form::
                // with('formQuestions.QuestionType')
                // ->with('formQuestions.QuestionChoices')
                where('user_id', Auth()->user()->id)
                ->orderBy('updated_at', 'desc')
                ->get();
            return $this->returnData('data', $forms);
        } catch (\Exception $e) {
            return $this->returnError('201', 'fail');
        }
    }

    public function getFormQuestions(Request $request)
    {
        try {
            if (!$request->has('form_id')) {
                return $this->returnError('202', 'fail');
            }
            $form = Form::with('formQuestions.QuestionType')
                ->with('formQuestions.QuestionChoices')
                ->with(['user' => function ($q) {
                    $q->select('id', 'name');
                }])
                ->where('id', $request->form_id)->first();
            return $this->returnData('data', $form);
        } catch (\Exception $e) {
            return $this->returnError('201', 'fail');
        }
    }

    public function deleteForm(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$request->has('form_id')) {
                return $this->returnError('202', 'fail');
            }
            $form = Form::find($request->form_id);
            if (!$form) {
                return $this->returnError('203', 'fail');
            }
            if ($form->user_id != Auth()->user()->id) {
                return $this->returnError('204', 'fail');
            }
            $form->delete();
            DB::commit();
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', 'fail');
        }
    }

    public function editForm(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$request->has('form_id')) {
                return $this->returnError('202', 'fail');
            }
            $form = Form::find($request->form_id);
            if (!$form) {
                return $this->returnError('203', 'fail');
            }
            if ($form->user_id != Auth()->user()->id) {
                return $this->returnError('204', 'fail');
            }
            $questions = $form->formQuestions;
            // return 4;
            foreach ($questions as $question) {
                if (isset($question->QuestionChoices) && count($question->QuestionChoices) > 0) {
                    $question->QuestionChoices()->delete();
                }
            }
            $request['form_type'] = $form['type'];
            $form->formQuestions()->delete();
            $form->delete();
            $this->createForm($request);
            DB::commit();
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function searchForms(Request $request)
    {
        try {
            if (!$request->has('title')) {
                return $this->returnError('202', 'fail');
            }
            $forms = Form::where('title', 'like', '%' . $request->title . '%')->get();
            return $this->returnData('data', $forms);
        } catch (\Exception $e) {
            return $this->returnError('201', 'fail');
        }
    }

    public function updateResponseMsg(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$request->has('response_msg') || !$request->has('form_id')) {
                return $this->returnError('202', 'fail');
            }
            $form = Form::find($request->form_id);
            if (!$form) {
                return $this->returnError('203', 'fail');
            }
            if ($form->user_id != Auth()->user()->id) {
                return $this->returnError('204', 'fail');
            }
            $form->update([
                'response_msg' => $request->response_msg
            ]);
            DB::commit();
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', 'fail');
        }
    }

    public function editAcceptingResponses(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$request->has('accept') || !$request->has('form_id')) {
                return $this->returnError('202', 'fail');
            }
            $form = Form::find($request->form_id);
            if (!$form) {
                return $this->returnError('203', 'fail');
            }
            if ($form->user_id != Auth()->user()->id) {
                return $this->returnError('204', 'fail');
            }
            $form->update([
                'accepting_responses' => $request->accept
            ]);
            DB::commit();
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', 'fail');
        }
    }
}
