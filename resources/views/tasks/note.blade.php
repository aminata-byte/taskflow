<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Note — {{ $task->title }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            font-family: 'Inter', sans-serif;
            background: white;
            height: 100%;
            color: #1a1a2e;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: white;
            border-bottom: 1px solid #e8eaf0;
            padding: 0 2rem;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        }

        .topbar-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .topbar-task {
            font-family: 'Sora', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: #1a1a2e;
        }

        .topbar-project {
            font-size: 0.78rem;
            color: #9090a8;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .save-btn {
            padding: 8px 24px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: opacity 0.15s, transform 0.1s;
        }

        .save-btn:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .save-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .close-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid #e0e0f0;
            background: white;
            color: #6b6b8a;
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
            text-decoration: none;
        }

        .close-btn:hover {
            background: #f5f5ff;
            color: #1a1a2e;
        }

        .page {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 0;
            overflow: hidden;
        }

        .note-card {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
            border: none;
            border-radius: 0;
            overflow: hidden;
        }

        textarea#note-content {
            flex: 1;
            width: 100%;
            border: none;
            outline: none;
            padding: 2rem 3rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            line-height: 1.75;
            color: #2d2d4e;
            background: white;
            resize: none;
        }

        textarea#note-content::placeholder {
            color: #b0b0c8;
        }

        .note-footer {
            padding: 0.8rem 3rem;
            border-top: 1px solid #f0f0f5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        #save-status {
            font-size: 0.82rem;
            font-weight: 600;
            min-height: 1.2rem;
        }

        .status-ok {
            color: #059669;
        }

        .status-err {
            color: #dc2626;
        }

        .last-saved {
            font-size: 0.75rem;
            color: #a0a0b8;
            margin-top: 4px;
        }

        .char-count {
            font-size: 0.75rem;
            color: #a0a0b8;
        }
    </style>
</head>

<body>

    <div class="topbar">
        <div class="topbar-info">
            <span class="topbar-task">{{ $task->title }}</span>
            <span class="topbar-project">{{ $task->column->project->title }}</span>
        </div>
        <div class="topbar-right">
            <button class="save-btn" onclick="saveNote()" id="save-btn">Sauvegarder</button>
            <a href="{{ route('projects.show', $task->column->project) }}" class="close-btn">×</a>
        </div>
    </div>

    <div class="page">
        <div class="note-card">
            <textarea id="note-content" placeholder="Écris tes notes ici...">{{ $note?->content ?? '' }}</textarea>

            <div class="note-footer">
                <div>
                    <span id="save-status"></span>
                    @if ($note?->updated_at)
                        <div class="last-saved">Dernière sauvegarde : {{ $note->updated_at->format('d/m/Y à H:i') }}
                        </div>
                    @endif
                </div>
                <span class="char-count" id="char-count">{{ strlen($note?->content ?? '') }} caractères</span>
            </div>
        </div>
    </div>

    <script>
        const textarea = document.getElementById('note-content');
        const charCount = document.getElementById('char-count');

        textarea.addEventListener('input', () => {
            charCount.textContent = textarea.value.length + ' caractères';
        });

        document.addEventListener('keydown', e => {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveNote();
            }
        });

        function saveNote() {
            const content = textarea.value;
            const btn = document.getElementById('save-btn');
            const status = document.getElementById('save-status');

            btn.disabled = true;
            btn.textContent = 'Sauvegarde...';
            status.className = '';
            status.textContent = '';

            fetch('/tasks/{{ $task->id }}/note', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        content
                    })
                })
                .then(r => r.json())
                .then(d => {
                    btn.disabled = false;
                    btn.textContent = 'Sauvegarder';
                    if (d.success) {
                        status.className = 'status-ok';
                        status.textContent = '✓ Sauvegardé';
                        const now = new Date();
                        const fmt = now.toLocaleDateString('fr-FR') + ' à ' + now.toLocaleTimeString('fr-FR', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        let lastSaved = document.querySelector('.last-saved');
                        if (!lastSaved) {
                            lastSaved = document.createElement('div');
                            lastSaved.className = 'last-saved';
                            status.parentElement.appendChild(lastSaved);
                        }
                        lastSaved.textContent = 'Dernière sauvegarde : ' + fmt;
                        setTimeout(() => {
                            status.textContent = '';
                        }, 3000);
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.textContent = 'Sauvegarder';
                    status.className = 'status-err';
                    status.textContent = '✗ Erreur, réessaie.';
                });
        }
    </script>
</body>

</html>
