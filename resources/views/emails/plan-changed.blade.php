@extends('layouts.email')

@section('css')
<style>
  .plan-card:hover { border-color: rgba(var(--primary), 0.3) !important; background-color: rgba(var(--primary), 0.05) !important; }
</style>
@endsection

@section('content')
<div style="display: inline-flex; align-items: center; gap: 8px; background-color: rgba(34, 197, 94, 0.1); color: #22c55e; padding-left: 12px; padding-right: 12px; padding-top: 4px; padding-bottom: 4px; border-radius: 9999px; font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">
    <span style="height: 6px; width: 6px; border-radius: 9999px; background-color: #22c55e;"></span> Subscription Updated
</div>

<div style="margin-top: 24px;">
    <h1 style="font-size: 24px; font-weight: 700; color: #ffffff; margin-bottom: 12px;">Plan Update Confirmation</h1>
    <p style="color: #cbd5e1; font-size: 14px; line-height: 1.625; margin-bottom: 16px;">Hello {{ $user->first_name }},</p>
    <p style="color: #cbd5e1; font-size: 14px; line-height: 1.625;">
        Your subscription plan has been updated by an administrator. You now have access to all the features included in your new plan.
    </p>
</div>

<div style="background-color: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 24px; margin-top: 32px; position: relative; overflow: hidden;">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 20px;">
        <div style="flex: 1;">
            <p style="font-size: 10px; color: #62748e; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-bottom: 8px;">New Plan</p>
            <h2 style="font-size: 20px; font-weight: 800; color: #ffffff; margin: 0;">{{ $newPlan }}</h2>
        </div>
        
        @if($oldPlan && $oldPlan !== $newPlan)
        <div style="color: rgba(255, 255, 255, 0.2);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </div>
        
        <div style="flex: 1; text-align: right;">
            <p style="font-size: 10px; color: #62748e; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-bottom: 8px;">Previous Plan</p>
            <p style="font-size: 16px; font-weight: 600; color: #94a3b8; margin: 0;">{{ $oldPlan }}</p>
        </div>
        @endif
    </div>
</div>

<div style="margin-top: 32px; text-align: center;">
    <a href="{{ config('app.url') }}/dashboard" style="display: inline-block; background-color: #ffffff; color: #020C17; padding: 12px 32px; border-radius: 12px; font-weight: 700; font-size: 14px; text-decoration: none; transition: all 0.2s ease;">
        Go to Dashboard
    </a>
</div>

<div style="border-top: 1px solid rgba(255, 255, 255, 0.05); margin-top: 40px; padding-top: 24px;">
    <p style="color: #62748e; font-size: 12px; line-height: 1.5; margin: 0;">
        If you have any questions about this change or notice anything unexpected, please contact our support team.
    </p>
</div>
@endsection
