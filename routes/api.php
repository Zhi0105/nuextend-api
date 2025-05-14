<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventMemberController;
use App\Http\Controllers\EventStatusController;
use App\Http\Controllers\EventTypeController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TargetgroupController;
use App\Http\Controllers\UnsdgController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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


    Route::middleware('auth:sanctum')->group(function () {

        // FORM UPLOADING START
            Route::get('/form/{id}', [FormController::class, 'index']);
            Route::post('/forms', [FormController::class, 'store']);
        // FORM UPLOADING END


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
        // PARTICIPANT END

    });

});

