<!DOCTYPE html>
<html lang="{{ $user->locale ?? 'en' }}" dir="{{ ($user->locale ?? 'en') === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; color: #1e293b; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #6366f1, #4f46e5); padding: 40px 32px; text-align: center; }
        .header h1 { color: #fff; font-size: 24px; font-weight: 700; }
        .header p { color: #c7d2fe; font-size: 14px; margin-top: 8px; }
        .body { padding: 32px; }
        .greeting { font-size: 16px; color: #374151; margin-bottom: 24px; }
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 32px; }
        .stat-card { background: #f8fafc; border-radius: 12px; padding: 16px; text-align: center; }
        .stat-label { font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
        .stat-value { font-size: 22px; font-weight: 700; }
        .stat-income { color: #10b981; }
        .stat-expense { color: #ef4444; }
        .stat-balance { color: #6366f1; }
        .section-title { font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
        .category-list { list-style: none; }
        .category-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
        .category-item:last-child { border-bottom: none; }
        .category-name { display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .category-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
        .category-amount { font-weight: 600; font-size: 14px; color: #ef4444; }
        .cta { margin-top: 32px; text-align: center; }
        .cta-btn { display: inline-block; background: #6366f1; color: #fff; padding: 14px 32px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 15px; }
        .footer { background: #f8fafc; padding: 24px 32px; border-top: 1px solid #e2e8f0; text-align: center; }
        .footer p { font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>💰 Finance Tracker</h1>
        <p>
            @if(($user->locale ?? 'en') === 'ar')
                ملخصك المالي لشهر {{ \Carbon\Carbon::create($year, $month)->locale('ar')->translatedFormat('F Y') }}
            @else
                Your {{ \Carbon\Carbon::create($year, $month)->format('F Y') }} Financial Summary
            @endif
        </p>
    </div>

    <div class="body">
        <p class="greeting">
            @if(($user->locale ?? 'en') === 'ar')
                مرحباً {{ $user->name }}،
            @else
                Hello {{ $user->name }},
            @endif
        </p>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-label">
                    {{ ($user->locale ?? 'en') === 'ar' ? 'الدخل' : 'Income' }}
                </div>
                <div class="stat-value stat-income">${{ number_format($summary['total_income'], 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">
                    {{ ($user->locale ?? 'en') === 'ar' ? 'المصروفات' : 'Expenses' }}
                </div>
                <div class="stat-value stat-expense">${{ number_format($summary['total_expenses'], 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">
                    {{ ($user->locale ?? 'en') === 'ar' ? 'الرصيد' : 'Balance' }}
                </div>
                <div class="stat-value stat-balance">${{ number_format($summary['net_balance'], 2) }}</div>
            </div>
        </div>

        @if(!empty($topCategories))
        <div>
            <p class="section-title">
                {{ ($user->locale ?? 'en') === 'ar' ? 'أعلى 5 فئات إنفاق' : 'Top 5 Spending Categories' }}
            </p>
            <ul class="category-list">
                @foreach($topCategories as $cat)
                <li class="category-item">
                    <div class="category-name">
                        <span class="category-dot" style="background-color: {{ $cat['color'] }}"></span>
                        {{ $cat['name'] }}
                    </div>
                    <span class="category-amount">${{ number_format($cat['total'], 2) }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="cta">
            <a href="{{ config('app.url') }}/reports" class="cta-btn">
                {{ ($user->locale ?? 'en') === 'ar' ? 'عرض التقرير الكامل' : 'View Full Report' }}
            </a>
        </div>
    </div>

    <div class="footer">
        <p>
            {{ ($user->locale ?? 'en') === 'ar'
                ? "أنت تتلقى هذا البريد لأن لديك حساباً في Finance Tracker"
                : "You're receiving this because you have an account at Finance Tracker" }}
        </p>
    </div>
</div>
</body>
</html>
