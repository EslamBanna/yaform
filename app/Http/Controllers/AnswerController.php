<?php

namespace App\Http\Controllers;

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
            // return $request;
            DB::commit();
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', 'fail');
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
