<?php

namespace App\Http\Controllers;

use App\Models\Declaration;
use App\Models\Employer;
use Illuminate\View\View;

class DeclarationInterfaceController extends Controller
{
    public function index(): View
    {
        $canManageDeclarations = auth()->user()?->roles()->whereIn('code', ['ADMIN', 'AGENT_SES'])->exists() ?? false;

        $employers = Employer::query()
            ->orderBy('legal_name')
            ->get(['id', 'affiliation_number', 'legal_name']);

        return view('declarations.index', [
            'employers' => $employers,
            'canManageDeclarations' => $canManageDeclarations,
        ]);
    }

    public function show(Declaration $declaration): View
    {
        $canManageDeclarations = auth()->user()?->roles()->whereIn('code', ['ADMIN', 'AGENT_SES'])->exists() ?? false;

        $declaration->load('employer');

        return view('declarations.show', [
            'declaration' => $declaration,
            'canManageDeclarations' => $canManageDeclarations,
        ]);
    }
}
