@if(($user->locale ?? 'en') === 'ar')
مرحباً {{ $user->name }}،

ملخصك المالي لشهر {{ \Carbon\Carbon::create($year, $month)->locale('ar')->translatedFormat('F Y') }}

الدخل: ${{ number_format($summary['total_income'], 2) }}
المصروفات: ${{ number_format($summary['total_expenses'], 2) }}
الرصيد الصافي: ${{ number_format($summary['net_balance'], 2) }}

@if(!empty($topCategories))
أعلى فئات الإنفاق:
@foreach($topCategories as $cat)
- {{ $cat['name'] }}: ${{ number_format($cat['total'], 2) }}
@endforeach
@endif

عرض التقرير الكامل: {{ config('app.url') }}/reports

أنت تتلقى هذا البريد لأن لديك حساباً في Finance Tracker
@else
Hello {{ $user->name }},

Your {{ \Carbon\Carbon::create($year, $month)->format('F Y') }} Financial Summary:

Income: ${{ number_format($summary['total_income'], 2) }}
Expenses: ${{ number_format($summary['total_expenses'], 2) }}
Net Balance: ${{ number_format($summary['net_balance'], 2) }}

@if(!empty($topCategories))
Top Spending Categories:
@foreach($topCategories as $cat)
- {{ $cat['name'] }}: ${{ number_format($cat['total'], 2) }}
@endforeach
@endif

View Full Report: {{ config('app.url') }}/reports

You're receiving this because you have an account at Finance Tracker.
@endif
