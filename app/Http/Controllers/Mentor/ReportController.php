<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Comment;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $stagiaires = Stagiaire::where('mentor_id', Auth::id())->pluck('id');
        $rapports   = Report::with('stagiaire.user', 'project')
            ->whereIn('stagiaire_id', $stagiaires)->latest()->paginate(15);
        return view('mentor.reports.index', compact('rapports'));
    }

    public function show(Report $report)
    {
        $stagiaire = $report->stagiaire;
        abort_if($stagiaire->mentor_id !== Auth::id(), 403);
        $report->load('stagiaire.user', 'project', 'comments.user', 'validePar');
        return view('mentor.reports.show', compact('report'));
    }

    public function evaluate(Report $report)
    {
        abort_if($report->stagiaire->mentor_id !== Auth::id(), 403);
        return view('mentor.reports.evaluate', compact('report'));
    }

    public function doEvaluate(Request $request, Report $report)
    {
        abort_if($report->stagiaire->mentor_id !== Auth::id(), 403);
        $request->validate([
            'statut'             => 'required|in:valide,rejete,en_revision',
            'note'               => 'nullable|integer|min:0|max:20',
            'commentaire_mentor' => 'nullable|string',
        ]);

        $report->update([
            'statut'             => $request->statut,
            'note'               => $request->note,
            'commentaire_mentor' => $request->commentaire_mentor,
            'valide_par'         => Auth::id(),
            'valide_le'          => now(),
        ]);

        return redirect()->route('mentor.reports.index')->with('succes', 'Rapport évalué avec succès.');
    }

    public function commenter(Request $request, Report $report)
    {
        abort_if($report->stagiaire->mentor_id !== Auth::id(), 403);
        $request->validate(['contenu' => 'required|string']);
        Comment::create([
            'user_id'   => Auth::id(),
            'report_id' => $report->id,
            'contenu'   => $request->contenu,
        ]);
        return back()->with('succes', 'Commentaire ajouté.');
    }

    public function telecharger(Report $report)
    {
        abort_if($report->stagiaire->mentor_id !== Auth::id(), 403);
        $chemin = storage_path('app/public/' . $report->fichier);
        abort_unless(file_exists($chemin), 404, 'Fichier introuvable.');
        return response()->download($chemin);
    }
}