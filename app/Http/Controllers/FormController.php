<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormQuestion;
use App\Models\MultipleChoiceQ;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Validator;
use DB;

class FormController extends Controller
{
    use GeneralTrait;
    public function createForm(Request $request)
    {
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
                        foreach ($question['choices'] as $choice) {
                            MultipleChoiceQ::create([
                                'question_id' => $question_id,
                                'choice' => $choice
                            ]);
                        }
                    } else if ($question['q_type'] == 9) {
                        // image
                    } else if ($question['q_type'] == 10) {
                        FormQuestion::create([
                            'form_id' => $form_id,
                            'title' => $question['title'],
                            'description' => $question['description'],
                            'type' => $question['q_type'],
                            // 'required' => $question->required ?? 0
                        ]);
                    } else {
                        FormQuestion::create([
                            'form_id' => $form_id,
                            'title' => $question['title'],
                            // 'description' => $question->description,
                            'type' => $question['q_type'],
                            'required' => $question['required'] ?? 0
                        ]);
                    }
                }
            }
            DB::commit();
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', $e->getMessage());
        }
    }
}
