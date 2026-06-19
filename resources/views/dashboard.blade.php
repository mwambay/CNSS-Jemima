@extends('layouts.app')

@section('title', 'Tableau de bord | CNSS')
@section('page_title', 'Tableau de bord')
@section('page_subtitle', 'Vue d ensemble operationnelle')

@section('content')
    <div style="display:grid; gap:1rem; grid-template-columns: repeat(3, minmax(0, 1fr));">
        <article style="background:#fff; border:1px solid #e7eef2; border-radius:14px; padding:1rem; box-shadow: 0 1px 3px rgba(6,52,109,.1);">
            <p style="margin:0; color:#667085; font-size:.84rem;">Employeurs actifs</p>
            <h3 style="margin:.5rem 0 0; font-size:1.55rem; color:#06346d;">-</h3>
        </article>
        <article style="background:#fff; border:1px solid #e7eef2; border-radius:14px; padding:1rem; box-shadow: 0 1px 3px rgba(6,52,109,.1);">
            <p style="margin:0; color:#667085; font-size:.84rem;">Declarations en attente</p>
            <h3 style="margin:.5rem 0 0; font-size:1.55rem; color:#008f83;">-</h3>
        </article>
        <article style="background:#fff; border:1px solid #e7eef2; border-radius:14px; padding:1rem; box-shadow: 0 1px 3px rgba(6,52,109,.1);">
            <p style="margin:0; color:#667085; font-size:.84rem;">Alertes fraude ouvertes</p>
            <h3 style="margin:.5rem 0 0; font-size:1.55rem; color:#06346d;">-</h3>
        </article>
    </div>

    <div style="margin-top:1rem; background:#fff; border:1px solid #e7eef2; border-radius:14px; padding:1rem; box-shadow:0 1px 3px rgba(6,52,109,.1);">
        <h2 style="margin:0 0 .5rem; font-size:1.05rem; color:#06346d;">Demarrage rapide</h2>
        <p style="margin:0; color:#667085; font-size:.92rem;">Utilisez la barre laterale pour acceder aux modules. Le module Employeurs est deja operationnel.</p>
    </div>
@endsection
