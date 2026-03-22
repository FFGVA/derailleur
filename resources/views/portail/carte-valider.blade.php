<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification membre — FFGVA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f1e9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
        }
        .card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            max-width: 380px;
            width: 100%;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        }
        .circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .circle-valid {
            background-color: #dcfce7;
            border: 4px solid #22c55e;
        }
        .circle-invalid {
            background-color: #fef2f2;
            border: 4px solid #ef4444;
        }
        .circle svg {
            width: 60px;
            height: 60px;
        }
        .member-name {
            font-size: 1.375rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .member-info {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .member-info-valid { color: #166534; }
        .member-info-invalid { color: #991b1b; }
        .member-date {
            font-size: 0.875rem;
            color: #666;
            margin-bottom: 1rem;
        }
        .brand {
            font-size: 0.75rem;
            color: #999;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="card">
        @if($valid && $member)
            <div class="circle circle-valid">
                <svg fill="none" stroke="#22c55e" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="member-name">{{ $member->first_name }} {{ $member->last_name }}</div>
            <div class="member-info member-info-valid">Membre active</div>
            @if($member->membership_end)
                <div class="member-date">Valide jusqu'au {{ $member->membership_end->format('d.m.Y') }}</div>
            @endif
        @else
            <div class="circle circle-invalid">
                <svg fill="none" stroke="#ef4444" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            @if($member)
                <div class="member-name">{{ $member->first_name }} {{ $member->last_name }}</div>
            @endif
            <div class="member-info member-info-invalid">{{ $reason }}</div>
        @endif
        <div class="brand">Fast and Female Geneva</div>
    </div>
</body>
</html>
