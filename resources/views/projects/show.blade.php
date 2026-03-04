@extends('layouts.app')
@section('title', $project->title)

@section('content')
    <div class="page-container" style="max-width:100%;">

        {{-- Breadcrumb --}}
        <div class="breadcrumb">
            <a href="{{ route('projects.index') }}">Projets</a>
            <span class="separator">›</span>
            <span>{{ $project->title }}</span>
        </div>

        {{-- En-tête du Kanban --}}
        <div class="kanban-header">
            <div>
                <h1 class="page-title">{{ $project->title }}</h1>
                @if ($project->description)
                    <p class="page-subtitle">{{ $project->description }}</p>
                @endif
            </div>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('projects.edit', $project) }}" class="btn-secondary">✏️ Modifier</a>
                <a href="{{ route('projects.index') }}" class="btn-secondary">← Retour</a>
            </div>
        </div>

        {{-- Message de succès --}}
        @if (session('success'))
            <div class="alert alert-success" style="max-width:500px;">✅ {{ session('success') }}</div>
        @endif

        {{-- Tableau Kanban --}}
        <div class="kanban-board">

            @foreach ($project->columns as $column)
                <div class="kanban-column">

                    {{-- En-tête colonne --}}
                    <div class="column-header">
                        <div class="column-title-wrap">
                            @php
                                $dotClass = match ($column->name) {
                                    'À faire' => 'todo',
                                    'En cours' => 'progress',
                                    'Terminé' => 'done',
                                    default => 'todo',
                                };
                            @endphp
                            <span class="column-dot {{ $dotClass }}"></span>
                            <span class="column-title">{{ $column->name }}</span>
                        </div>
                        <span class="column-count">{{ $column->tasks->count() }}</span>
                    </div>

                    {{-- Liste des tâches --}}
                    <div class="column-body">
                        @forelse($column->tasks as $task)
                            @php
                                $priorityClass = match ($task->priority ?? 'basse') {
                                    'haute' => 'priority-haute',
                                    'moyenne' => 'priority-moyenne',
                                    default => 'priority-basse',
                                };
                            @endphp

                            <div class="task-card {{ $priorityClass }}">

                                <p class="task-title">{{ $task->title }}</p>

                                @if ($task->description)
                                    <p class="task-description">{{ Str::limit($task->description, 80) }}</p>
                                @endif

                                @if ($task->due_date)
                                    @php
                                        $isOverdue =
                                            \Carbon\Carbon::parse($task->due_date)->isPast() &&
                                            $column->name !== 'Terminé';
                                    @endphp
                                    <div class="task-due {{ $isOverdue ? 'overdue' : '' }}">
                                        {{ $isOverdue ? '🔥' : '📅' }}
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                        {{ $isOverdue ? '— En retard !' : '' }}
                                    </div>
                                @endif

                                <div class="task-footer">
                                    <span class="priority-badge {{ $task->priority ?? 'basse' }}">
                                        {{ ucfirst($task->priority ?? 'basse') }}
                                    </span>
                                    <div class="task-actions">
                                        <a href="{{ route('columns.tasks.edit', [$column, $task]) }}" class="task-btn edit"
                                            title="Modifier">✏️</a>
                                        <form action="{{ route('columns.tasks.destroy', [$column, $task]) }}"
                                            method="POST" onsubmit="return confirm('Supprimer cette tâche ?')"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="task-btn delete" title="Supprimer">🗑️</button>
                                        </form>
                                    </div>
                                </div>

                            </div>

                        @empty
                            <div style="text-align:center; padding:2rem 0; color:var(--text-muted); font-size:0.85rem;">
                                <div style="font-size:1.8rem; margin-bottom:0.5rem; opacity:0.4;">
                                    {{ $column->name === 'Terminé' ? '🏁' : '📭' }}
                                </div>
                                Aucune tâche
                            </div>
                        @endforelse
                    </div>

                    {{-- Formulaire ajout tâche --}}
                    <div class="task-add-form">
                        <form action="{{ route('columns.tasks.store', $column) }}" method="POST">
                            @csrf
                            <input type="text" name="title" placeholder="+ Ajouter une tâche..." required>
                            <select name="priority">
                                <option value="basse">🟢 Priorité basse</option>
                                <option value="moyenne">🟡 Priorité moyenne</option>
                                <option value="haute">🔴 Priorité haute</option>
                            </select>
                            <input type="date" name="due_date" style="color:var(--text-muted);">
                            <button type="submit" class="btn-add-task">＋ Ajouter</button>
                        </form>
                    </div>

                </div>
            @endforeach

        </div>

    </div>
@endsection
