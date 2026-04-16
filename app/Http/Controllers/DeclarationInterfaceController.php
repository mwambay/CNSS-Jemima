<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use Illuminate\View\View;

class DeclarationInterfaceController extends Controller
{
    public function index(): View
    {
        $canManageDeclarations = auth()->user()?->roles()->where('code', 'ADMIN')->exists() ?? false;

        $employers = Employer::query()
            ->orderBy('legal_name')
            ->get(['id', 'affiliation_number', 'legal_name']);

        return view('declarations.index', [
            'employers' => $employers,
            'canManageDeclarations' => $canManageDeclarations,
        ]);
    }
}
