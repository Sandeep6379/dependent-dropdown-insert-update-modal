<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>
<body>


<div class="container">
<h2>Student Add</h2>
<form action="" method="POST">
    @csrf
    <input type="text" name="name" placeholder="name" required>
    <br><br>
    <select name="subject_id" id="subjects" required>
        <option value="">Select Subject</option>
        @foreach($subjects as $subject)
        <option value="{{ $subject->id }}">{{$subject->name}}</option>
        @endforeach
    </select>
    <br><br>
    <select name="plan_id" id="plans" required>
        <option value="">Select Plan</option>
    </select>
    <br><br>
    <input type="submit">
</form>

<table border=1 width="100%" cellspacing="0">

    <tr>
        <th>S.No</th>
        <th>Name</th>
        <th>Subject</th>
        <th>Plan</th>
        <th>Action</th>
    </tr>
    @if(count($students) > 0)
        @foreach($students as $student)
            <tr>
                <td>{{$student->id}}</td>
                <td>{{$student->name}}</td>
                <td>{{$student['subject']['name']}}</td>
                <td>{{$student['plan']['plan']}}</td>
                <td>
                    <button class="editButton" data-id="{{$student->id}}" data-toggle="modal" data-target="#editStudentModal">Edit</button>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="5">No Record Found!</td>
        </tr>
    @endif

</table>
</div>

<!-- Edit Studet Modal -->

<div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Edit Student</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="updateStudent">
      @csrf
            <div class="modal-body">
                <input type="hidden" name="id" value="" id="student_id">
                <input type="text" name="name" placeholder="name" value="" id="student_name" required>
                <br><br>
                <select name="subject_id" id="edit_subjects" required>
                    <option value="">Select Subject</option>
                </select>
                <br><br>
                <select name="plan_id" id="edit_plans" required>
                    <option value="">Select Plan</option>
                </select>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>

    $(document).ready(function(){

        $("#subjects").change(function(){

            var subject_id = $(this).val();
            if(subject_id == ""){
                $("#plans").html("<option value=''>Select Plan</option>");
            }

            $.ajax({
                url:"/get-plans/"+subject_id,
                type:"GET",
                success:function(data){
                    var plans = data.plans;
                    var html = "<option value=''>Select Plan</option>";
                    for(let i =0;i<plans.length;i++){
                        html += `
                        <option value="`+plans[i]['id']+`">`+plans[i]['plan']+`</option>
                        `;
                    }
                    $("#plans").html(html);
                }
            });

        });


        //edit student
        $(".editButton").click(function(){

            var student_id = $(this).attr('data-id');
            $.ajax({
                url:"{{ route('editStudentLoad') }}",
                type:"GET",
                data:{id:student_id},
                success:function(data) {
                    var studentPlans = data.plans;
                    var studentSubjects = data.subjects;
                    var student = data.student;
                    var htmlSubject = "<option value=''>Select Subject</option>";
                    var htmlPlan = "<option value=''>Select Plan</option>";
                    $("#student_id").val(student[0]['id']);
                    $("#student_name").val(student[0]['name']);

                    for(let i = 0;i < studentSubjects.length;i++){
                        if(student[0]['subject_id'] == studentSubjects[i]['id']){
                            htmlSubject += `<option value="`+studentSubjects[i]['id']+`" selected>`+studentSubjects[i]['name']+`</option>`;
                        }
                        else{
                            htmlSubject += `<option value="`+studentSubjects[i]['id']+`">`+studentSubjects[i]['name']+`</option>`;
                        }
                    }

                    for(let x = 0;x < studentPlans.length;x++){
                        if(student[0]['plan_id'] == studentPlans[x]['id']){
                            htmlPlan += `<option value="`+studentPlans[x]['id']+`" selected>`+studentPlans[x]['plan']+`</option>`;
                        }
                        else{
                            htmlPlan += `<option value="`+studentPlans[x]['id']+`">`+studentPlans[x]['plan']+`</option>`;
                        }
                    }

                    $("#edit_subjects").html(htmlSubject);
                    $("#edit_plans").html(htmlPlan);
                }
            });

        });

        //load plans for modals

        $("#edit_subjects").change(function(){

            var subject_id = $(this).val();
            if(subject_id == ""){
                $("#plans").html("<option value=''>Select Plan</option>");
            }

            $.ajax({
                url:"/get-plans/"+subject_id,
                type:"GET",
                success:function(data){
                    var plans = data.plans;
                    var html = "<option value=''>Select Plan</option>";
                    for(let i =0;i<plans.length;i++){
                        html += `
                        <option value="`+plans[i]['id']+`">`+plans[i]['plan']+`</option>
                        `;
                    }
                    $("#edit_plans").html(html);
                }
            });

        });

        //update student by ajax
        $("#updateStudent").submit(function(){

            var formData = $(this).serialize();

            $.ajax({
                url:"{{route('updateStudent')}}",
                type:"POST",
                data:formData,
                success:function(data){
                    if(data.success == true){
                        location.reload();
                    }
                    else{
                        alert(data.msg);
                    }
                }
            });

        });

    });

</script>


    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
</body>
</html>