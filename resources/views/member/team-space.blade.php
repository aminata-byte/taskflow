@extends('layouts.app')

@section('title', 'Espace Équipe')

@section('content')
    <div class="page-container">

        @foreach ($workspaces as $workspace)
            @php $wsIndex = $loop->index; @endphp

            {{-- HEADER --}}
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ $workspace['team']->name }}</h1>
                    <p class="page-subtitle">Vos tâches assignées</p>
                </div>
                <a href="{{ route('workspace.choose') }}" class="btn-secondary">Changer d'espace</a>
            </div>

            {{-- MES TÂCHES (drag & drop) --}}
            <div class="card" style="margin-bottom:2rem;">

                <h2 style="font-family:'Sora',sans-serif; font-size:1.1rem; font-weight:700; margin-bottom:1.5rem;">
                    {{ $workspace['project']->title }}
                </h2>

                <div
                    style="display:grid; grid-template-columns: repeat({{ $workspace['columns']->count() }}, 1fr); gap:1rem;">
                    @foreach ($workspace['columns'] as $column)
                        <div class="kanban-column" data-column-id="{{ $column->id }}" ondragover="allowDrop(event)"
                            ondrop="dropTask(event, {{ $column->id }}, {{ $wsIndex }})"
                            ondragenter="this.style.background='rgba(99,102,241,0.08)'"
                            ondragleave="this.style.background=''">

                            <div class="column-header">
                                <span class="column-title">{{ $column->name }}</span>
                                <span class="column-count" id="count-{{ $wsIndex }}-{{ $column->id }}">
                                    {{ isset($workspace['myTasks'][$column->id]) ? $workspace['myTasks'][$column->id]->count() : 0 }}
                                </span>
                            </div>

                            <div class="tasks-list" id="list-{{ $wsIndex }}-{{ $column->id }}"
                                style="min-height:60px;">
                                @if (isset($workspace['myTasks'][$column->id]) && $workspace['myTasks'][$column->id]->count() > 0)
                                    @foreach ($workspace['myTasks'][$column->id] as $task)
                                        <div class="task-card" draggable="true" data-task-id="{{ $task->id }}"
                                            data-ws="{{ $wsIndex }}"
                                            ondragstart="dragStart(event, {{ $task->id }}, {{ $wsIndex }})"
                                            ondragend="dragEnd(event)">

                                            <div class="task-title">{{ $task->title }}</div>

                                            @if ($task->description)
                                                <div class="task-desc">{{ Str::limit($task->description, 60) }}</div>
                                            @endif

                                            <div
                                                style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                                                @if ($task->due_date)
                                                    @php $isLate = \Carbon\Carbon::parse($task->due_date)->isPast(); @endphp
                                                    <span
                                                        style="font-size:0.75rem; color:{{ $isLate ? '#F87171' : 'var(--text-muted)' }}">
                                                        {{ $isLate ? '' : '' }}
                                                        {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                                    </span>
                                                @else
                                                    <span></span>
                                                @endif
                                                @if ($task->priority)
                                                    <span
                                                        class="priority-badge priority-{{ $task->priority }}">{{ strtoupper($task->priority) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="empty-col-msg"
                                        style="color:var(--text-muted); font-size:0.8rem; text-align:center; padding:1rem 0.5rem;">
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
                        Tâches de l'équipe — {{ $workspace['team']->name }} (lecture seule)
                    </h2>

                    @foreach ($workspace['teamTasks'] as $memberId => $data)
                        <div style="margin-bottom:2rem;">
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:1rem;">
                                <div
                                    style="width:34px; height:34px; background:var(--accent-grad); border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.85rem; color:white;">
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
                                            style="font-size:0.8rem; font-weight:600; color:var(--text-secondary); margin-bottom:0.8rem; text-transform:uppercase;">
                                            {{ $column->name }}
                                        </div>
                                        @if (isset($data['tasks'][$column->id]) && $data['tasks'][$column->id]->count() > 0)
                                            @foreach ($data['tasks'][$column->id] as $task)
                                                <div
                                                    style="background:var(--bg-card); border-radius:8px; padding:10px; margin-bottom:8px; border:1px solid var(--border); opacity:0.85;">
                                                    <div style="font-size:0.875rem; font-weight:600;">{{ $task->title }}
                                                    </div>
                                                    @if ($task->priority)
                                                        <span class="priority-badge priority-{{ $task->priority }}"
                                                            style="margin-top:6px; display:inline-block;">{{ strtoupper($task->priority) }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div
                                                style="color:var(--text-muted); font-size:0.8rem; text-align:center; padding:0.5rem;">
                                                Aucune tâche</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if (!$loop->last)
                <hr style="border:none; border-top:1px solid var(--border); margin:2rem 0;">
            @endif
        @endforeach

    </div>
@endsection

@push('scripts')
    <script>
        let draggedTaskId = null;
        let draggedWsIndex = null;

        function allowDrop(event) {
            event.preventDefault();
        }

        function dragStart(event, taskId, wsIndex) {
            draggedTaskId = taskId;
            draggedWsIndex = wsIndex;
            event.target.style.opacity = '0.4';
            event.dataTransfer.effectAllowed = 'move';
        }

        function dragEnd(event) {
            event.target.style.opacity = '1';
            document.querySelectorAll('.kanban-column').forEach(c => c.style.background = '');
        }

        function dropTask(event, columnId, wsIndex) {
            event.preventDefault();
            document.querySelectorAll('.kanban-column').forEach(c => c.style.background = '');
            if (!draggedTaskId || draggedWsIndex !== wsIndex) return;

            const taskEl = document.querySelector(`[data-task-id="${draggedTaskId}"]`);
            const targetList = document.getElementById(`list-${wsIndex}-${columnId}`);
            if (!taskEl || !targetList) return;
            if (taskEl.parentElement === targetList) return;

            const sourceList = taskEl.parentElement;
            targetList.appendChild(taskEl);
            showOrHideEmpty(targetList);
            showOrHideEmpty(sourceList);
            updateCount(wsIndex, columnId);
            const sourceColId = sourceList.id.split('-').pop();
            updateCount(wsIndex, sourceColId);

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
                    if (!data.success) {
                        sourceList.appendChild(taskEl);
                        showOrHideEmpty(targetList);
                        showOrHideEmpty(sourceList);
                        updateCount(wsIndex, columnId);
                        updateCount(wsIndex, sourceColId);
                    }
                })
                .catch(() => location.reload());

            draggedTaskId = null;
            draggedWsIndex = null;
        }

        function showOrHideEmpty(listEl) {
            if (!listEl) return;
            const hasTasks = listEl.querySelectorAll('.task-card').length > 0;
            const existing = listEl.querySelector('.empty-col-msg');
            if (hasTasks) {
                if (existing) existing.remove();
            } else if (!existing) {
                const div = document.createElement('div');
                div.className = 'empty-col-msg';
                div.style.cssText = 'color:var(--text-muted); font-size:0.8rem; text-align:center; padding:1rem 0.5rem;';
                div.textContent = 'Aucune tâche';
                listEl.appendChild(div);
            }
        }

        function updateCount(wsIndex, columnId) {
            const list = document.getElementById(`list-${wsIndex}-${columnId}`);
            const badge = document.getElementById(`count-${wsIndex}-${columnId}`);
            if (list && badge) badge.textContent = list.querySelectorAll('.task-card').length;
        }
    </script>
@endpush
