<?php

use App\Http\Livewire\AgencyLivewire;
use App\Http\Livewire\ApplicantDocsLivewire;
use App\Http\Livewire\ApplicantsLivewire;
use App\Http\Livewire\ApplicationFromLivewire;
use App\Http\Livewire\Blacklist;
use App\Http\Livewire\Cases;
use App\Http\Livewire\Complaints;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Login;
use App\Http\Livewire\OFWMonitoring;
use App\Http\Livewire\Reporting;
use App\Http\Livewire\Users;
use App\Http\Livewire\Vouchers;
use Gridjs\ApplicantTableGridjs;
use Gridjs\DocumentTableGridjs;
use Gridjs\OFWMonitoringGridjs;
use Gridjs\VoucherGridjs;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', Login::class)->name('login');
Route::post(
    '/logout',
    function () {
        $agency = auth()->user()->agency_id;
        Auth::logout();

        return redirect()->route('login', ['agency' => $agency]);
    }
)->name('logout');

Route::get('report', Reporting::class)->name('report');

Route::middleware(['auth'])->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');

    Route::middleware(['can:agency'])->group(function () {
        Route::get('/vouchers', Vouchers::class)->name('finance.vouchers');
        Route::post('/vouchers/get', function () {
            return app(VoucherGridjs::class)->fetch(request());
        })->name('vouchers.get');

        Route::get('/applicants', ApplicantsLivewire::class)->name('applicants');
        Route::get('/applicant/form', ApplicationFromLivewire::class)->name('applicant.form');
        Route::post('/applicant/get', function () {
            return app(ApplicantTableGridjs::class)->fetch(request());
        })->name('applicant.get');

        Route::get('/applicant/docs', ApplicantDocsLivewire::class)->name('applicant-docs-livewire');
        Route::post('/docs/get', function () {
            return app(DocumentTableGridjs::class)->fetch(request());
        })->name('docs.get');
    });

    Route::middleware(['can:admin'])->group(function () {
        Route::get('/ofw-monitoring', OFWMonitoring::class)->name('ofw.monitoring');
        Route::post('/ofw-monitoring/fetch', function () {
            return app(OFWMonitoringGridjs::class)->fetch(request());
        })->name('ofw-monitoring.fetch');

        Route::get('/users', Users::class)->name('users');
        Route::get('/agencies', AgencyLivewire::class)->name('agencies');
        Route::get('/blacklist', Blacklist::class)->name('blacklist');
        Route::get('/complaints', Complaints::class)->name('complaints');
        Route::get('/cases', Cases::class)->name('cases');
    });
});
