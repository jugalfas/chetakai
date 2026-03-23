@extends('layouts.email')

@section('content')
<div style="display: inline-flex; align-items: center; gap: 8px; background-color: rgba(236, 179, 101, 0.1); color: #ECB365; padding-left: 12px; padding-right: 12px; padding-top: 4px; padding-bottom: 4px; border-radius: 9999px; font-size: 12px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;"><span style="height: 6px; width: 6px; border-radius: 9999px; background-color: #ECB365; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></span>Official Reply</div>
<div style="margin-top: 24px;">
    <h1 style="font-size: 24px; font-weight: 700; color: #ffffff; margin-bottom: 12px;">Hello {{ $name }},</h1>
    <p style="color: #cbd5e1; font-size: 14px; line-height: 1.625;">Thank you for reaching out to us. We have reviewed your inquiry and here is our response:</p>
</div>

<div style="background-color: rgba(255, 255, 255, 0.05); border-radius: 16px; padding: 24px; border: 1px solid rgba(255, 255, 255, 0.1); margin-bottom: 40px;">
    <div style="margin-bottom: 24px;">
        <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin: 0 0 12px 0;">Our Response</p>
        <div style="color: #ffffff; line-height: 1.625; font-size: 16px;">
            {!! nl2br(str_replace('\\n', "<br>", $replyMessage)) !!}
        </div>
    </div>

    <div style="height: 1px; background-color: rgba(255, 255, 255, 0.05); width: 100%; margin-bottom: 24px;"></div>

    <div style="margin-bottom: 0;">
        <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin: 0 0 12px 0;">Your Original Message</p>
        <div style="color: #94a3b8; line-height: 1.625; font-style: italic; border-left: 2px solid #063654; padding-left: 16px; padding-top: 4px; padding-bottom: 4px; white-space: pre-wrap; font-size: 14px;">"{!! nl2br(str_replace('\\n', "<br>", $originalMessage)) !!}"</div>
    </div>
</div>

<div style="text-align: center; margin-bottom: 40px;">
    <p style="color: #cbd5e1; font-size: 14px; margin-bottom: 24px;">If you have any further questions, feel free to reply to this email.</p>
    <a href="mailto:{{ config('mail.from.address') }}" style="display: inline-block; height: 56px; line-height: 56px; padding-left: 40px; padding-right: 40px; border-radius: 12px; background-color: #063654; color: #ffffff; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; font-size: 12px; border: none; text-decoration: none; box-shadow: 0 20px 25px -5px rgba(6, 54, 84, 0.2);">
        Contact Support
    </a>
</div>
@endsection
