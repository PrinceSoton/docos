<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\MentorManagementController;
use App\Http\Controllers\Admin\StagiaireManagementController;
use App\Http\Controllers\Admin\PresenceController as AdminPresence;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\EvenementManagementController;
use App\Http\Controllers\Admin\AttestationManagementController;
use App\Http\Controllers\Admin\CalendarManagementController;
use App\Http\Controllers\Admin\ArchiveManagementController;
use App\Http\Controllers\Mentor\DashboardController as MentorDashboard;
use App\Http\Controllers\Mentor\StagiaireController as MentorStagiaire;
use App\Http\Controllers\Mentor\PresenceController as MentorPresence;
use App\Http\Controllers\Mentor\ProjectController as MentorProject;
use App\Http\Controllers\Mentor\TaskController as MentorTask;
use App\Http\Controllers\Mentor\ReportController as MentorReport;
use App\Http\Controllers\Mentor\EvenementController as MentorEvenement;
use App\Http\Controllers\Mentor\AttestationController as MentorAttestation;
use App\Http\Controllers\Stagiaire\DashboardController as StagDashboard;
use App\Http\Controllers\Stagiaire\PresenceController as StagPresence;
use App\Http\Controllers\Stagiaire\ReportController as StagReport;
use App\Http\Controllers\Stagiaire\TaskController as StagTask;
use App\Http\Controllers\Stagiaire\ProjectController as StagProject;
use App\Http\Controllers\Stagiaire\EvenementController as StagEvenement;
use App\Http\Controllers\Stagiaire\AttestationController as StagAttestation;
use App\Http\Controllers\Auth\PasswordChangeController;

// ─── Page d'accueil ───────────────────────────────────────────────────────────
Route::get('/', fn() => view('welcome'))->name('welcome');

// ─── Authentification ─────────────────────────────────────────────────────────
Route::get('/connexion', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/connexion', [AuthenticatedSessionController::class, 'store']);
Route::post('/deconnexion', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// ─── Routes authentifiées ─────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    // Route du changement de mot de passe lors de la 1ère connexion
    Route::get('/password/change', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/change', [PasswordChangeController::class, 'update'])->name('password.update');

    // ─── Profil (tous rôles) ───────────────────────────────────────────────
    Route::prefix('profil')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/modifier', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/modifier', [ProfileController::class, 'update'])->name('update');
        Route::put('/mot-de-passe', [ProfileController::class, 'updatePassword'])->name('password');
    });

    // ─── Documents (tous rôles) ────────────────────────────────────────────
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/creer', [DocumentController::class, 'create'])->name('create');
        Route::post('/', [DocumentController::class, 'store'])->name('store');
        Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
        Route::get('/{document}/modifier', [DocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}', [DocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{document}/telecharger', [DocumentController::class, 'telecharger'])->name('telecharger');
    });

    // ═══════════════════════════════════════════════════════════════════════
    // ─── ADMIN ────────────────────────────────────────────────────────────
    // ═══════════════════════════════════════════════════════════════════════
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {

        // Tableau de bord
        Route::get('/tableau-de-bord', [AdminDashboard::class, 'index'])->name('dashboard');

        // Utilisateurs
        Route::prefix('utilisateurs')->name('users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::get('/creer', [UserManagementController::class, 'create'])->name('create');
            Route::post('/', [UserManagementController::class, 'store'])->name('store');
            Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
            Route::get('/{user}/modifier', [UserManagementController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
            Route::patch('/{user}/toggle', [UserManagementController::class, 'toggleActif'])->name('toggle');
        });

        // Mentors
        Route::prefix('mentors')->name('mentors.')->group(function () {
            Route::get('/', [MentorManagementController::class, 'index'])->name('index');
            Route::get('/creer', [MentorManagementController::class, 'create'])->name('create');
            Route::post('/', [MentorManagementController::class, 'store'])->name('store');
            Route::get('/affecter', [MentorManagementController::class, 'assign'])->name('assign');
            Route::post('/affecter', [MentorManagementController::class, 'doAssign'])->name('doAssign');
            Route::get('/{mentor}/modifier', [MentorManagementController::class, 'edit'])->name('edit');
            Route::put('/{mentor}', [MentorManagementController::class, 'update'])->name('update');
            Route::delete('/{mentor}', [MentorManagementController::class, 'destroy'])->name('destroy');
            Route::patch('/stagiaires/{stagiaire}/retirer', [MentorManagementController::class, 'removeAssign'])->name('removeAssign');
        });

        // Stagiaires
        Route::prefix('stagiaires')->name('stagiaires.')->group(function () {
            Route::get('/', [StagiaireManagementController::class, 'index'])->name('index');
            Route::get('/creer', [StagiaireManagementController::class, 'create'])->name('create');
            Route::post('/', [StagiaireManagementController::class, 'store'])->name('store');
            Route::get('/{stagiaire}', [StagiaireManagementController::class, 'show'])->name('show');
            Route::get('/{stagiaire}/modifier', [StagiaireManagementController::class, 'edit'])->name('edit');
            Route::put('/{stagiaire}', [StagiaireManagementController::class, 'update'])->name('update');
            Route::delete('/{stagiaire}', [StagiaireManagementController::class, 'destroy'])->name('destroy');
        });

        // Présences
        Route::prefix('presences')->name('presences.')->group(function () {
            Route::get('/', [AdminPresence::class, 'index'])->name('index');
            Route::get('/stagiaires/{stagiaire}', [AdminPresence::class, 'show'])->name('show');
        });

        // Attestations
        Route::prefix('attestations')->name('attestations.')->group(function () {
            Route::get('/', [AttestationManagementController::class, 'index'])->name('index');
            Route::get('/{attestation}', [AttestationManagementController::class, 'show'])->name('show');
            Route::get('/{attestation}/envoyer', [AttestationManagementController::class, 'uploadForm'])->name('uploadForm');
            Route::put('/{attestation}/envoyer', [AttestationManagementController::class, 'upload'])->name('upload');
            Route::get('/{attestation}/telecharger', [AttestationManagementController::class, 'telecharger'])->name('telecharger');
            Route::delete('/{attestation}', [AttestationManagementController::class, 'destroy'])->name('destroy');
        });

        // Archives
        Route::prefix('archives')->name('archives.')->group(function () {
            Route::get('/', [ArchiveManagementController::class, 'index'])->name('index');
            Route::get('/creer', [ArchiveManagementController::class, 'create'])->name('create');
            Route::post('/', [ArchiveManagementController::class, 'store'])->name('store');
            Route::get('/{archive}', [ArchiveManagementController::class, 'show'])->name('show');
            Route::get('/{archive}/modifier', [ArchiveManagementController::class, 'edit'])->name('edit');
            Route::put('/{archive}', [ArchiveManagementController::class, 'update'])->name('update');
            Route::delete('/{archive}', [ArchiveManagementController::class, 'destroy'])->name('destroy');
            Route::delete('/fichiers/{fichier}', [ArchiveManagementController::class, 'supprimerFichier'])->name('supprimerFichier');
            Route::get('/fichiers/{fichier}/telecharger', [ArchiveManagementController::class, 'telecharger'])->name('telecharger');
        });

        // Calendrier
        Route::prefix('calendrier')->name('calendars.')->group(function () {
            Route::get('/', [CalendarManagementController::class, 'index'])->name('index');
            Route::get('/creer', [CalendarManagementController::class, 'create'])->name('create');
            Route::post('/', [CalendarManagementController::class, 'store'])->name('store');
            Route::get('/{calendar}/modifier', [CalendarManagementController::class, 'edit'])->name('edit');
            Route::put('/{calendar}', [CalendarManagementController::class, 'update'])->name('update');
            Route::delete('/{calendar}', [CalendarManagementController::class, 'destroy'])->name('destroy');
            Route::put('/config/jours', [CalendarManagementController::class, 'updateConfig'])->name('updateConfig');
        });

        // Événements
        Route::prefix('evenements')->name('evenements.')->group(function () {
            Route::get('/', [EvenementManagementController::class, 'index'])->name('index');
            Route::get('/creer', [EvenementManagementController::class, 'create'])->name('create');
            Route::post('/', [EvenementManagementController::class, 'store'])->name('store');
            Route::get('/{evenement}', [EvenementManagementController::class, 'show'])->name('show');
            Route::get('/{evenement}/modifier', [EvenementManagementController::class, 'edit'])->name('edit');
            Route::put('/{evenement}', [EvenementManagementController::class, 'update'])->name('update');
            Route::delete('/{evenement}', [EvenementManagementController::class, 'destroy'])->name('destroy');
        });

        // Statistiques
        Route::get('/statistiques', [StatisticsController::class, 'index'])->name('statistics.index');
    });

    // ═══════════════════════════════════════════════════════════════════════
    // ─── MENTOR ───────────────────────────────────────────────────────────
    // ═══════════════════════════════════════════════════════════════════════
    Route::prefix('mentor')->name('mentor.')->middleware('mentor')->group(function () {

        Route::get('/tableau-de-bord', [MentorDashboard::class, 'index'])->name('dashboard');

        // Stagiaires du mentor
        Route::prefix('stagiaires')->name('stagiaires.')->group(function () {
            Route::get('/', [MentorStagiaire::class, 'index'])->name('index');
            Route::get('/{stagiaire}', [MentorStagiaire::class, 'show'])->name('show');
        });

        // Présences + Permissions
        Route::prefix('presences')->name('presences.')->group(function () {
            Route::get('/', [MentorPresence::class, 'index'])->name('index');
            Route::get('/stagiaires/{stagiaire}', [MentorPresence::class, 'show'])->name('show');
            Route::patch('/permissions/{permission}/valider', [MentorPresence::class, 'validerPermission'])->name('validerPermission');
        });

        // Projets
        Route::prefix('projets')->name('projects.')->group(function () {
            Route::get('/', [MentorProject::class, 'index'])->name('index');
            Route::get('/creer', [MentorProject::class, 'create'])->name('create');
            Route::post('/', [MentorProject::class, 'store'])->name('store');
            Route::get('/{project}', [MentorProject::class, 'show'])->name('show');
            Route::get('/{project}/modifier', [MentorProject::class, 'edit'])->name('edit');
            Route::put('/{project}', [MentorProject::class, 'update'])->name('update');
            Route::delete('/{project}', [MentorProject::class, 'destroy'])->name('destroy');
        });

        // Tâches
        Route::prefix('taches')->name('tasks.')->group(function () {
            Route::get('/', [MentorTask::class, 'index'])->name('index');
            Route::get('/creer', [MentorTask::class, 'create'])->name('create');
            Route::post('/', [MentorTask::class, 'store'])->name('store');
            Route::get('/{task}/modifier', [MentorTask::class, 'edit'])->name('edit');
            Route::put('/{task}', [MentorTask::class, 'update'])->name('update');
            Route::delete('/{task}', [MentorTask::class, 'destroy'])->name('destroy');
        });

        // Rapports
        Route::prefix('rapports')->name('reports.')->group(function () {
            Route::get('/', [MentorReport::class, 'index'])->name('index');
            Route::get('/{report}', [MentorReport::class, 'show'])->name('show');
            Route::get('/{report}/evaluer', [MentorReport::class, 'evaluate'])->name('evaluate');
            Route::put('/{report}/evaluer', [MentorReport::class, 'doEvaluate'])->name('doEvaluate');
            Route::post('/{report}/commenter', [MentorReport::class, 'commenter'])->name('commenter');
            Route::get('/{report}/telecharger', [MentorReport::class, 'telecharger'])->name('telecharger');
        });

        // Événements
        Route::prefix('evenements')->name('evenements.')->group(function () {
            Route::get('/', [MentorEvenement::class, 'index'])->name('index');
            Route::get('/{evenement}', [MentorEvenement::class, 'show'])->name('show');
        });

        // Attestations
        Route::prefix('attestations')->name('attestations.')->group(function () {
            Route::get('/', [MentorAttestation::class, 'index'])->name('index');
            Route::get('/{attestation}', [MentorAttestation::class, 'show'])->name('show');
            Route::get('/{attestation}/valider', [MentorAttestation::class, 'validate'])->name('validate');
            Route::put('/{attestation}/valider', [MentorAttestation::class, 'doValidate'])->name('doValidate');
            Route::get('/{attestation}/telecharger', [MentorAttestation::class, 'telecharger'])->name('telecharger');
        });
    });

    // ═══════════════════════════════════════════════════════════════════════
    // ─── STAGIAIRE ────────────────────────────────────────────────────────
    // ═══════════════════════════════════════════════════════════════════════
    Route::prefix('stagiaire')->name('stagiaire.')->middleware('stagiaire')->group(function () {

        Route::get('/tableau-de-bord', [StagDashboard::class, 'index'])->name('dashboard');

        // Présence + Permissions
        Route::prefix('presence')->name('presence.')->group(function () {
            Route::get('/', [StagPresence::class, 'index'])->name('index');
            Route::post('/marquer', [StagPresence::class, 'marquer'])->name('marquer');
            Route::post('/permission', [StagPresence::class, 'demandePermission'])->name('demandePermission');
            Route::get('/{presence}', [StagPresence::class, 'show'])->name('show');
            Route::get('/{presence}/justificatif', [StagPresence::class, 'telechargerJustificatif'])->name('telechargerJustificatif');
        });

        // Rapports
        Route::prefix('rapports')->name('reports.')->group(function () {
            Route::get('/', [StagReport::class, 'index'])->name('index');
            Route::get('/creer', [StagReport::class, 'create'])->name('create');
            Route::post('/', [StagReport::class, 'store'])->name('store');
            Route::get('/{report}', [StagReport::class, 'show'])->name('show');
            Route::get('/{report}/modifier', [StagReport::class, 'edit'])->name('edit');
            Route::put('/{report}', [StagReport::class, 'update'])->name('update');
            Route::delete('/{report}', [StagReport::class, 'destroy'])->name('destroy');
            Route::get('/{report}/telecharger', [StagReport::class, 'telecharger'])->name('telecharger');
        });

        // Tâches
        Route::prefix('taches')->name('tasks.')->group(function () {
            Route::get('/', [StagTask::class, 'index'])->name('index');
            Route::get('/{task}', [StagTask::class, 'show'])->name('show');
            Route::patch('/{task}/statut', [StagTask::class, 'updateStatut'])->name('updateStatut');
        });

        // Projets
        Route::prefix('projets')->name('projects.')->group(function () {
            Route::get('/', [StagProject::class, 'index'])->name('index');
            Route::get('/{project}', [StagProject::class, 'show'])->name('show');
        });

        // Événements
        Route::prefix('evenements')->name('evenements.')->group(function () {
            Route::get('/', [StagEvenement::class, 'index'])->name('index');
            Route::get('/{evenement}', [StagEvenement::class, 'show'])->name('show');
            Route::get('/{evenement}/telecharger', [StagEvenement::class, 'telecharger'])->name('telecharger');
        });

        // Attestations
        Route::prefix('attestations')->name('attestations.')->group(function () {
            Route::get('/', [StagAttestation::class, 'index'])->name('index');
            Route::get('/demande', [StagAttestation::class, 'request'])->name('request');
            Route::post('/', [StagAttestation::class, 'store'])->name('store');
            Route::get('/{attestation}/telecharger', [StagAttestation::class, 'telecharger'])->name('telecharger');
        });
    });
});
