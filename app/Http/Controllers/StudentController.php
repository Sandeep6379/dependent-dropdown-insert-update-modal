<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Plan;
use App\Models\Subject;

class StudentController extends Controller
{
    public function loadAddStudent()
    {
        $subjects = Subject::all();
        $students = Student::with('subject','plan')->get();
        // echo "<pre>";
        // print_r($students);
        // dd();
        return view('student',['subjects'=>$subjects,'students'=>$students]);
    }

    public function addStudent(Request $request)
    {
        $student = new Student;
        $student->name = $request->name;
        $student->subject_id = $request->subject_id;
        $student->plan_id = $request->plan_id;
        $student->save();

        return back();
    }

    public function getPlans($subject_id)
    {
        $plans = Plan::where('subject_id',$subject_id)->get();
        return response()->json(['plans'=>$plans]);
    }

    public function editStudentLoad(Request $request)
    {
        $student = Student::where('id',$request->id)->get();
        $subjects = Subject::all();
        $plans = Plan::where('subject_id',$student[0]['subject_id'])->get();

        return response()->json(['student'=>$student,'subjects'=>$subjects,'plans'=>$plans]);
    }

    public function updateStudent(Request $request)
    {
        $student = Student::find($request->id);
        $student->name = $request->name;
        $student->subject_id = $request->subject_id;
        $student->plan_id = $request->plan_id;
        $student->save();

        return response()->json(['success'=>true,'msg'=>'Student updated successfully!']);
    }
}