@extends('layouts.app')

@section('title', 'Espace Équipe')

@section('content')
    <div class="page-container">

        @foreach ($workspaces as $workspace)
            {{-- HEADER --}}
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ $workspace['team']->name }}</h1>
                    <p class="page-subtitle">{{ $workspace['project']->title }} • Vos tâches assignées</p>
                </div>
                <a href="{{ route('workspace.choose') }}" class="btn-secondary">← Changer d'espace</a>
            </div>

            {{-- MES TÂCHES (drag & drop) --}}
            <div class="card" style="margin-bottom:2rem;">
                <h2 style="font-family:'Sora',sans-serif; font-size:1.1rem; font-weight:700; margin-bottom:1.5rem;">
                    🗂 Mes tâches — {{ $workspace['project']->title }}
                </h2>

                <div
                    style="display:grid; grid-template-columns: repeat({{ $workspace['columns']->count() }}, 1fr); gap:1rem;">
                    @foreach ($workspace['columns'] as $column)
                        <div class="kanban-column" data-column-id="{{ $column->id }}" ondragover="event.preventDefault()"
                            ondrop="dropTask(event, {{ $column->id }})">

                            <div class="column-header">
                                <span class="column-title">{{ $column->name }}</span>
                                <span class="column-count">
                                    {{ isset($workspace['myTasks'][$column->id]) ? $workspace['myTasks'][$column->id]->count() : 0 }}
                                </span>
                            </div>

                            <div class="tasks-list" id="column-{{ $column->id }}">
                                @if (isset($workspace['myTasks'][$column->id]))
                                    @foreach ($workspace['myTasks'][$column->id] as $task)
                                        <div class="task-card" draggable="true" data-task-id="{{ $task->id }}"
                                            ondragstart="dragStart(event, {{ $task->id }})">

                                            <div class="task-title">{{ $task->title }}</div>

                                            @if ($task->description)
                                                <div class="task-desc">{{ Str::limit($task->description, 60) }}</div>
                                            @endif

                                            <div
                                                style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                                                @if ($task->due_date)
                                                    <span
                                                        style="font-size:0.75rem; color:{{ $task->due_date->isPast() ? '#F87171' : 'var(--text-muted)' }}">
                                                        {{ $task->due_date->format('d/m/Y') }}
                                                    </span>
                                                @endif
                                                @if ($task->priority)
                                                    <span class="priority-badge priority-{{ $task->priority }}">
                                                        {{ $task->priority }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div
                                        style="color:var(--text-muted); font-size:0.8rem; text-align:center; padding:1rem;">
                                        Aucune tâche
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- TÂCHES DES AUTRES MEMBRES (lecture seule) --}}
            @if (!empty($workspace['teamTasks']))
                <div class="card" style="margin-bottom:2.5rem;">
                    <h2 style="font-family:'Sora',sans-serif; font-size:1.1rem; font-weight:700; margin-bottom:1.5rem;">
                        👥 Tâches de l'équipe — {{ $workspace['team']->name }} (lecture seule)
                    </h2>

                    @foreach ($workspace['teamTasks'] as $memberId => $data)
                        <div style="margin-bottom:2rem;">

                            {{-- Avatar membre --}}
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:1rem;">
                                <div
                                    style="width:34px; height:34px; background:var(--accent-grad); border-radius:50%;
                                        display:flex; align-items:center; justify-content:center;
                                        font-weight:700; font-size:0.85rem; color:white;">
                                    {{ strtoupper(substr($data['user']->name, 0, 1)) }}
                                </div>
                                <span style="font-weight:600;">{{ $data['user']->name }}</span>
                            </div>

                            <div
                                style="display:grid; grid-template-columns: repeat({{ $workspace['columns']->count() }}, 1fr); gap:1rem;">
                                @foreach ($workspace['columns'] as $column)
                                    <div
                                        style="background:var(--bg-column); border-radius:12px; padding:1rem; border:1px solid var(--border);">
                                        <div
                                            style="font-size:0.8rem; font-weight:600; color:var(--text-secondary);
                                                margin-bottom:0.8rem; text-transform:uppercase;">
                                            {{ $column->name }}
                                        </div>

                                        @if (isset($data['tasks'][$column->id]))
                                            @foreach ($data['tasks'][$column->id] as $task)
                                                <div
                                                    style="background:var(--bg-card); border-radius:8px; padding:10px;
                                                        margin-bottom:8px; border:1px solid var(--border); opacity:0.85;">
                                                    <div style="font-size:0.875rem; font-weight:600;">{{ $task->title }}
                                                    </div>
                                                    @if ($task->priority)
                                                        <span class="priority-badge priority-{{ $task->priority }}"
                                                            style="margin-top:6px; display:inline-block;">
                                                            {{ $task->priority }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div
                                                style="color:var(--text-muted); font-size:0.8rem; text-align:center; padding:0.5rem;">
                                                Aucune tâche
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Séparateur entre espaces si plusieurs équipes --}}
            @if (!$loop->last)
                <hr style="border:none; border-top:1px solid var(--border); margin:2rem 0;">
            @endif
        @endforeach

    </div>
@endsection

@push('scripts')
    <script>
        let draggedTaskId = null;

        function dragStart(event, taskId) {
            draggedTaskId = taskId;
            event.target.style.opacity = '0.5';
        }

        function dropTask(event, columnId) {
            event.preventDefault();
            if (!draggedTaskId) return;

            // Déplacer visuellement
            const taskEl = document.querySelector(`[data-task-id="${draggedTaskId}"]`);
            if (taskEl) {
                taskEl.style.opacity = '1';
                const targetList = document.getElementById(`column-${columnId}`);
                if (targetList) targetList.appendChild(taskEl);
            }

            // Envoyer au serveur
            fetch('{{ route('member.move-task') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        task_id: draggedTaskId,
                        column_id: columnId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) updateCounts();
                })
                .catch(() => location.reload());

            draggedTaskId = null;
        }

        function updateCounts() {
            document.querySelectorAll('.kanban-column').forEach(col => {
                const count = col.querySelectorAll('.task-card').length;
                const badge = col.querySelector('.column-count');
                if (badge) badge.textContent = count;
            });
        }

        document.addEventListener('dragend', function(e) {
            if (e.target.classList.contains('task-card')) {
                e.target.style.opacity = '1';
            }
        });
    </script>
@endpush
