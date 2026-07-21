<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Calendar;
use App\Models\ConfigJoursTravail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarManagementController extends Controller
{
    public function index()
    {
        $jours    = Calendar::orderBy('date')->paginate(20);
        $config   = ConfigJoursTravail::first();
        return view('admin.calendars.index', compact('jours', 'config'));
    }

    public function create()
    {
        return view('admin.calendars.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'        => 'required|date|unique:calendars,date',
            'libelle'     => 'required|string|max:200',
            'type'        => 'required|in:ferie,sejour,autre',
            'description' => 'nullable|string',
        ]);

        Calendar::create([
            'date'        => $request->date,
            'libelle'     => $request->libelle,
            'type'        => $request->type,
            'description' => $request->description,
            'cree_par'    => Auth::id(),
        ]);

        return redirect()->route('admin.calendars.index')->with('succes', 'Jour ajouté au calendrier.');
    }

    public function edit(Calendar $calendar)
    {
        return view('admin.calendars.edit', compact('calendar'));
    }

    public function update(Request $request, Calendar $calendar)
    {
        $request->validate([
            'date'        => 'required|date|unique:calendars,date,' . $calendar->id,
            'libelle'     => 'required|string|max:200',
            'type'        => 'required|in:ferie,sejour,autre',
            'description' => 'nullable|string',
        ]);

        $calendar->update($request->only('date', 'libelle', 'type', 'description'));
        return redirect()->route('admin.calendars.index')->with('succes', 'Jour mis à jour.');
    }

    public function destroy(Calendar $calendar)
    {
        $calendar->delete();
        return redirect()->route('admin.calendars.index')->with('succes', 'Jour supprimé.');
    }

    public function updateConfig(Request $request)
    {
        $request->validate([
            'lundi'       => 'boolean',
            'mardi'       => 'boolean',
            'mercredi'    => 'boolean',
            'jeudi'       => 'boolean',
            'vendredi'    => 'boolean',
            'samedi'      => 'boolean',
            'dimanche'    => 'boolean',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin'   => 'required|date_format:H:i|after:heure_debut',
        ]);

        $config = ConfigJoursTravail::firstOrCreate([]);
        $config->update([
            'lundi'       => $request->boolean('lundi'),
            'mardi'       => $request->boolean('mardi'),
            'mercredi'    => $request->boolean('mercredi'),
            'jeudi'       => $request->boolean('jeudi'),
            'vendredi'    => $request->boolean('vendredi'),
            'samedi'      => $request->boolean('samedi'),
            'dimanche'    => $request->boolean('dimanche'),
            'heure_debut' => $request->heure_debut,
            'heure_fin'   => $request->heure_fin,
        ]);

        return redirect()->route('admin.calendars.index')->with('succes', 'Configuration mise à jour.');
    }
}