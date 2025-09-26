<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventMemberController;
use App\Http\Controllers\EventStatusController;
use App\Http\Controllers\EventTypeController;
use App\Http\Controllers\Form1Controller;
use App\Http\Controllers\Form2Controller;
use App\Http\Controllers\Form3Controller;
use App\Http\Controllers\Form4Controller;
use App\Http\Controllers\Form5Controller;
use App\Http\Controllers\Form6Controller;
use App\Http\Controllers\Form7Controller;
use App\Http\Controllers\Form8Controller;
use App\Http\Controllers\Form9Controller;
use App\Http\Controllers\Form10Controller;
use App\Http\Controllers\Form11Controller;
use App\Http\Controllers\Form12Controller;
use App\Http\Controllers\Form14Controller;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OutreachProposalController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProgramProposalController;
use App\Http\Controllers\ProgressReportController;
use App\Http\Controllers\ProjectProposalController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TargetgroupController;
use App\Http\Controllers\UnsdgController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::get('/', function () {
        return 'NU Extend API';
    });

    Route::post('/authenticate', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);


    Route::get('/role/all', [RoleController::class, 'index']);
    Route::get('/department/all', [DepartmentController::class, 'index']);
    Route::get('/program/all', [ProgramController::class, 'index']);
    Route::get('/model/all', [ModelController::class, 'index']);
    Route::get('/event_types/all', [EventTypeController::class, 'index']);
    Route::get('/unsdg/all', [UnsdgController::class, 'index']);
    Route::get('/event_status/all', [EventStatusController::class, 'index']);
    Route::get('/organization/all', [OrganizationController::class, 'index']);
    Route::get('/skill/all', [SkillController::class, 'index']);
    Route::get('/targetgroup/all', [TargetgroupController::class, 'index']);
    Route::get('/participant/events/{id}', [ParticipantController::class, 'getParticipantEvents']);


    // EMAIL VERIFICATION
        Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])->middleware('auth:sanctum')->name('verification.send');;
        Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
        Route::get('/email/verify-status', [EmailVerificationController::class, 'verifyStatus'])->middleware('auth:sanctum');
    // EMAIL VERIFICATION


    Route::middleware(['auth:sanctum', 'verified'])->group(function () {

        // FORM UPLOADING START
            Route::get('/form/{id}', [FormController::class, 'index']);
            Route::get('/form/program/{model_id}', [FormController::class, 'getProgramForm']);
            Route::post('/forms', [FormController::class, 'store']);
            Route::delete('/forms/{id}', [FormController::class, 'delete']);
            Route::post('/form/approve', [FormController::class, 'approve']);
            Route::post('/form/reject', [FormController::class, 'reject']);
            Route::post('/form/attachment', [FormController::class, 'attachToEvent']);
        // FORM UPLOADING END

        // PROGRESS REPORT UPLOAD
            Route::get('/progress-report/{id}', [ProgressReportController::class, 'index']);
            Route::post('/progress-report', [ProgressReportController::class, 'store']);
            Route::post('/progress-report/remove', [ProgressReportController::class, 'delete']);
        // PROGRESS REPORT UPLOAD

        // PROGRESS REPORT APPROVAL
            Route::post('/progress-report/approve', [ProgressReportController::class, 'approve']);
            Route::post('/progress-report/reject', [ProgressReportController::class, 'reject']);
        // PROGRESS REPORT APPROVAL

        // USER START
            Route::get('/user/all', [AuthController::class, 'index']);
            Route::get('/user/{id}', [AuthController::class, 'getUser']);
            Route::post('/user/update/{id}', [AuthController::class, 'update']);
            Route::post('/user/delete', [AuthController::class, 'delete']);
            Route::post('/user/organization_assign', [UserController::class, 'organization_assign']);

        // USER END

        // DEPARTMENT START
            Route::post('/department/create', [DepartmentController::class, 'create']);
            Route::post('/department/update', [DepartmentController::class, 'update']);
            Route::post('/department/delete', [DepartmentController::class, 'delete']);

        // DEPARTMENT END

        // PROGRAM START
            Route::post('/program/create', [ProgramController::class, 'create']);
            Route::post('/program/update', [ProgramController::class, 'update']);
            Route::post('/program/delete', [ProgramController::class, 'delete']);
        // PROGRAM END

        // SKILLS START
            Route::post('/skill/create', [SkillController::class, 'create']);
            Route::post('/skill/update', [SkillController::class, 'update']);
            Route::post('/skill/delete', [SkillController::class, 'delete']);
        // SKILLS END



        // ORGANIZATION START
            Route::post('/organization/create', [OrganizationController::class, 'create']);
            Route::post('/organization/update', [OrganizationController::class, 'update']);
            Route::post('/organization/delete', [OrganizationController::class, 'delete']);
            Route::get('/organizations/{userID}', [OrganizationController::class, 'getOrganization']);
            Route::get('/organization/{id}/members', [OrganizationController::class, 'members']);
            Route::post('/organization/role/change', [OrganizationController::class, 'role_change']);
            Route::post('/organization/remove_member', [OrganizationController::class, 'remove_member']);
            // ORGANIZATION END

        // EVENT START
            Route::get('/event/all', [EventController::class, 'index']);
            Route::post('/event/create', [EventController::class, 'create']);
            Route::post('/event/update', [EventController::class, 'update']);
            Route::post('/event/delete', [EventController::class, 'delete']);
            Route::get('/event/{userID}', [EventController::class, 'getEvent']);
            Route::post('/event/accept', [EventController::class, 'accept']);
            Route::post('/event/reject', [EventController::class, 'reject']);
            Route::post('/event/post', [EventController::class, 'posted']);
            Route::post('/event/terminate', [EventController::class, 'terminate']);

        // EVENT END

        // EVENT MEMBER START
            Route::get('/event_member/all', [EventMemberController::class, 'index']);
            Route::post('/event_member/create', [EventMemberController::class, 'create']);
            Route::post('/event_member/update', [EventMemberController::class, 'update']);
            Route::post('/event_member/delete', [EventMemberController::class, 'delete']);
       // EVENT MEMBER END

        // PARTICIPANT START
            Route::get('/participant/all', [ParticipantController::class, 'index']);
            Route::post('/participant/create', [ParticipantController::class, 'create']);
            Route::post('/participant/update', [ParticipantController::class, 'update']);
            Route::post('/participant/delete', [ParticipantController::class, 'delete']);
            Route::post('/participant/attendance', [ParticipantController::class, 'attendance']);
        // PARTICIPANT END

        // PROPOSALS REQUEST
            Route::get('outreach/proposal', [OutreachProposalController::class, 'index']);
            Route::post('outreach/proposal/create', [OutreachProposalController::class, 'create']);
            Route::post('outreach/proposal/{id}', [OutreachProposalController::class, 'update']);

            Route::get('project/proposal', [ProjectProposalController::class, 'index']);
            Route::post('project/proposal/create', [ProjectProposalController::class, 'create']);
            Route::post('project/proposal/{id}', [ProjectProposalController::class, 'update']);

            Route::get('program/proposal', [ProgramProposalController::class, 'index']);
            Route::post('program/proposal/create', [ProgramProposalController::class, 'create']);
            Route::post('program/proposal/{id}', [ProgramProposalController::class, 'update']);
        // PROPOSALS REQUEST


        // UPDATED FORM REQUEST
            Route::get('form1/proposal', [Form1Controller::class, 'index']);
            Route::post('form1/proposal/create', [Form1Controller::class, 'create']);
            Route::post('form1/proposal/{id}', [Form1Controller::class, 'update']);
            Route::post('/form1/approve', [Form1Controller::class, 'approve']);
            Route::post('/form1/reject', [Form1Controller::class, 'reject']);

            Route::get('form2/proposal', [Form2Controller::class, 'index']);
            Route::post('form2/proposal/create', [Form2Controller::class, 'create']);
            Route::post('form2/proposal/{id}', [Form2Controller::class, 'update']);
            Route::post('/form2/approve', [Form2Controller::class, 'approve']);
            Route::post('/form2/reject', [Form2Controller::class, 'reject']);

            Route::get('form3/proposal', [Form3Controller::class, 'index']);
            Route::post('form3/proposal/create', [Form3Controller::class, 'create']);
            Route::post('form3/proposal/{id}', [Form3Controller::class, 'update']);
            Route::post('/form3/approve', [Form3Controller::class, 'approve']);
            Route::post('/form3/reject', [Form3Controller::class, 'reject']);
        // UPDATED FORM REQUEST

        // FORM 4 REQUEST
            Route::get('form4', [Form4Controller::class, 'index']);
            Route::post('form4/create', [Form4Controller::class, 'create']);
            Route::post('/form4/approve', [Form4Controller::class, "approve"]);
            Route::post('/form4/reject', [Form4Controller::class, 'reject']);
            Route::post('form4/{id}', [Form4Controller::class, 'update']);
        // FORM 4 REQUEST

        // FORM 5 REQUEST
            Route::get('form5', [Form5Controller::class, 'index']);
            Route::post('form5/create', [Form5Controller::class, 'create']);
            Route::post('/form5/approve', [Form5Controller::class, "approve"]);
            Route::post('/form5/reject', [Form5Controller::class, 'reject']);
            Route::post('form5/{id}', [Form5Controller::class, 'update']);
        // FORM 5 REQUEST

        // FORM 6 REQUEST
            Route::get('form6', [Form6Controller::class, 'index']);
            Route::post('form6/create', [Form6Controller::class, 'create']);
            Route::post('form6/{id}', [Form6Controller::class, 'update']);
        // FORM 6 REQUEST

        // FORM 7 REQUEST
            Route::get('form7', [Form7Controller::class, 'index']);
            Route::post('form7/create', [Form7Controller::class, 'create']);
            Route::post('form7/{id}', [Form7Controller::class, 'update']);
        // FORM 7 REQUEST

        // FORM 8 REQUEST
            Route::get('form8', [Form8Controller::class, 'index']);
            Route::post('form8/create', [Form8Controller::class, 'create']);
            Route::post('form8/{id}', [Form8Controller::class, 'update']);
        // FORM 8 REQUEST

        // FORM 9 REQUEST
            Route::get('form9', [Form9Controller::class, 'index']);
            Route::post('form9/create', [Form9Controller::class, 'create']);
            Route::post('form9/{id}', [Form9Controller::class, 'update']);
        // FORM 9 REQUEST

        // FORM 10 REQUEST
            Route::get('form10', [Form10Controller::class, 'index']);
            Route::post('form10/create', [Form10Controller::class, 'create']);
            Route::post('form10/{id}', [Form10Controller::class, 'update']);
        // FORM 10 REQUEST

        // FORM 11 REQUEST
            Route::get('form11', [Form11Controller::class, 'index']);
            Route::post('form11/create', [Form11Controller::class, 'create']);
            Route::post('form11/{id}', [Form11Controller::class, 'update']);
        // FORM 11 REQUEST

        // FORM 12 REQUEST
            Route::get('form12', [Form12Controller::class, 'index']);
            Route::post('form12/create', [Form12Controller::class, 'create']);
            Route::post('form12/{id}', [Form12Controller::class, 'update']);
        // FORM 12 REQUEST

        // FORM 14 REQUEST
            Route::get('form14/proposal', [Form14Controller::class, 'index']);
            Route::post('form14/proposal/create', [Form14Controller::class, 'store']);
            Route::get('form14/proposal/{id}', [Form14Controller::class, 'show']);
            Route::put('form14/proposal/{id}', [Form14Controller::class, 'update']);
            Route::delete('form14/proposal/{id}', [Form14Controller::class, 'destroy']);
            Route::get('form14/activity/{activities_id}', [Form14Controller::class, 'getReportsByActivity']);
            Route::patch('form14/{id}/status', [Form14Controller::class, 'updateStatus']);
            // FORM 14 REQUEST
    });
});

