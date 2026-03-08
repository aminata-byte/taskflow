@extends('layouts.app')

@section('content')
    <div class="page-container">

        <div class="page-header">
            <div>
                <h1 class="page-title"></h1> {{ $team->name }}</h1>
                <p class="page-subtitle">{{ $team->description ?? 'Aucune description' }} • Projet :
                    {{ $team->project?->title ?? 'Sans projet' }}</p>
            </div>
            <a href="{{ route('admin.teams.index') }}" class="btn-secondary">← Retour</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Progression des membres --}}
        <div class="card" style="margin-bottom:1.5rem;">
            <h2 style="font-family:'Sora',sans-serif; font-size:1.1rem; font-weight:700; margin-bottom:1.5rem;">
                Progression des membres</h2>

            @forelse($membersStats as $stat)
                <div style="margin-bottom:1.5rem; padding-bottom:1.5rem; border-bottom:1px solid var(--border);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div
                                style="width:38px; height:38px; background:var(--accent-grad); border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700;">
                                {{ strtoupper(substr($stat['user']->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;">{{ $stat['user']->name }}</div>
                                <div style="color:var(--text-muted); font-size:0.78rem;">{{ $stat['user']->email }}</div>
                            </div>
                        </div>
                        <div style="display:flex; gap:8px;">
                            <span
                                style="background:rgba(99,102,241,0.15); color:#818CF8; padding:3px 10px; border-radius:20px; font-size:0.78rem; font-weight:700;">
                                {{ $stat['todo'] }}</span>
                            <span
                                style="background:rgba(245,158,11,0.15); color:#FBBF24; padding:3px 10px; border-radius:20px; font-size:0.78rem; font-weight:700;">
                                {{ $stat['inprogress'] }}</span>
                            <span
                                style="background:rgba(16,185,129,0.15); color:#34D399; padding:3px 10px; border-radius:20px; font-size:0.78rem; font-weight:700;">
                                {{ $stat['done'] }}</span>
                        </div>
                    </div>
                    @php
                        $pct = $stat['total'] > 0 ? round(($stat['done'] / $stat['total']) * 100) : 0;
                    @endphp
                    <div style="background:rgba(255,255,255,0.05); border-radius:20px; height:8px; overflow:hidden;">
                        <div
                            style="background:var(--accent-grad); height:100%; width:{{ $pct }}%; border-radius:20px;">
                        </div>
                    </div>
                    <div style="color:var(--text-muted); font-size:0.75rem; margin-top:4px;">{{ $pct }}% complété
                        • {{ $stat['total'] }} tâches au total</div>
                </div>
            @empty
                <div class="empty-state">
                    <span class="empty-icon"></span>
                    <p class="empty-title">Aucun membre dans cette équipe</p>
                </div>
            @endforelse
        </div>

        {{-- Assigner des tâches --}}
        @if ($team->project && $team->members->isNotEmpty())
            <div class="card">
                <h2 style="font-family:'Sora',sans-serif; font-size:1.1rem; font-weight:700; margin-bottom:1.5rem;">
                    Assigner des tâches</h2>

                @foreach ($team->project->columns as $column)
                    @if ($column->tasks->isNotEmpty())
                        <div style="margin-bottom:1.2rem;">
                            <h3
                                style="color:var(--text-secondary); font-size:0.85rem; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:10px;">
                                {{ $column->name }}</h3>
                            @foreach ($column->tasks as $task)
                                <div
                                    style="display:flex; align-items:center; justify-content:space-between; padding:10px 14px; background:rgba(255,255,255,0.03); border-radius:10px; margin-bottom:8px; border:1px solid var(--border);">
                                    <div>
                                        <span style="font-weight:600; font-size:0.9rem;">{{ $task->title }}</span>
                                        @if ($task->assignedUser)
                                            <span style="color:var(--accent-1); font-size:0.78rem; margin-left:8px;">→
                                                {{ $task->assignedUser->name }}</span>
                                        @endif
                                    </div>
                                    <form action="{{ route('admin.teams.assign-task') }}" method="POST"
                                        style="display:flex; align-items:center; gap:8px;">
                                        @csrf
                                        <input type="hidden" name="task_id" value="{{ $task->id }}">
                                        <select name="user_id" class="form-control"
                                            style="width:auto; padding:6px 10px; font-size:0.82rem;">
                                            <option value="">-- Assigner à --</option>
                                            @foreach ($team->members as $member)
                                                <option value="{{ $member->id }}"
                                                    {{ $task->assigned_to == $member->id ? 'selected' : '' }}>
                                                    {{ $member->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn-primary btn-sm"></button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

    </div>
@endsection
