@extends('layouts.email')

@section('content')
    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 4px 12px; border-radius: 9999px; background-color: #ebb06633; border: 1px solid rgba(6, 54, 84, 0.3); margin-bottom: 24px;">
        <span style="height: 8px; width: 8px; border-radius: 9999px; background-color: #ebb066;"></span>
        <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #ebb066;">Official Reply</span>
    </div>
    
    <h3 style="font-size: 24px; font-weight: 700; color: #ffffff; margin-bottom: 24px; letter-spacing: -0.025em; line-height: 1.25; font-family: 'Josefin Sans', sans-serif; margin-top: 0;">
        Hello {{ $name }},
    </h3>
    
    <p style="color: #cbd5e1; margin-bottom: 40px; line-height: 1.625; font-size: 18px; font-weight: 300; margin-top: 0;">
        Thank you for reaching out to us. We have reviewed your inquiry and here is our response:
    </p>
    
    <div style="background-color: rgba(255, 255, 255, 0.05); border-radius: 16px; padding: 24px; border: 1px solid rgba(255, 255, 255, 0.1); margin-bottom: 40px;">
        <div style="margin-bottom: 24px;">
            <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin: 0 0 12px 0;">Our Response</p>
            <div style="color: #ffffff; line-height: 1.625; font-size: 16px;">
                {!! $replyMessage !!}
            </div>
        </div>
        
        <div style="height: 1px; background-color: rgba(255, 255, 255, 0.05); width: 100%; margin-bottom: 24px;"></div>
        
        <div style="margin-bottom: 0;">
            <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin: 0 0 12px 0;">Your Original Message</p>
            <div style="color: #94a3b8; line-height: 1.625; font-style: italic; border-left: 2px solid #063654; padding-left: 16px; padding-top: 4px; padding-bottom: 4px; white-space: pre-wrap; font-size: 14px;">"{{ $originalMessage }}"</div>
        </div>
    </div>
    
    <div style="text-align: center; margin-bottom: 40px;">
        <p style="color: #cbd5e1; font-size: 14px; margin-bottom: 24px;">If you have any further questions, feel free to reply to this email.</p>
        <a href="mailto:{{ config('mail.from.address') }}" style="display: inline-block; height: 56px; line-height: 56px; padding-left: 40px; padding-right: 40px; border-radius: 12px; background-color: #063654; color: #ffffff; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; font-size: 12px; border: none; text-decoration: none; box-shadow: 0 20px 25px -5px rgba(6, 54, 84, 0.2);">
            Contact Support
        </a>
    </div>
@endsection
