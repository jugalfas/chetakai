@extends('layouts.email')

@section('content')
    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 4px 12px; border-radius: 9999px; background-color: #ebb06633; border: 1px solid rgba(6, 54, 84, 0.3); margin-bottom: 24px;">
        <span style="height: 8px; width: 8px; border-radius: 9999px; background-color: #ebb066;"></span>
        <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #ebb066;">New Submission</span>
    </div>
    
    <h3 style="font-size: 24px; font-weight: 700; color: #ffffff; margin-bottom: 24px; letter-spacing: -0.025em; line-height: 1.25; font-family: 'Josefin Sans', sans-serif; margin-top: 0;">
        You've got a new lead waiting!
    </h3>
    
    <p style="color: #cbd5e1; margin-bottom: 40px; line-height: 1.625; font-size: 18px; font-weight: 300; margin-top: 0;">
        A potential user just reached out through your website contact form. Here are the details:
    </p>
    
    <div style="background-color: rgba(255, 255, 255, 0.05); border-radius: 16px; padding: 24px; border: 1px solid rgba(255, 255, 255, 0.1); margin-bottom: 40px;">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
            <div style="height: 40px; width: 40px; border-radius: 12px; background-color: rgba(235, 176, 102, 0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#EBB066" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 20px; width: 20px;">
                    <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path>
                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                </svg>
            </div>
            <div>
                <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin: 0;">From</p>
                <p style="color: #ffffff; font-weight: 500; margin: 0;">
                    {{ $data['name'] }} <span style="color: #94a3b8; font-weight: 400; margin-left: 4px;">&lt;{{ $data['email'] }}&gt;</span>
                </p>
            </div>
        </div>
        
        <div style="height: 1px; background-color: rgba(255, 255, 255, 0.05); width: 100%; margin-bottom: 24px;"></div>
        
        <div style="margin-bottom: 0;">
            <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin: 0 0 12px 0;">Message</p>
            <div style="color: #e2e8f0; line-height: 1.625; font-style: italic; border-left: 2px solid #063654; padding-left: 16px; padding-top: 4px; padding-bottom: 4px; white-space: pre-wrap;">"{{ $data['message'] }}"</div>
        </div>
    </div>
    
    <div style="text-align: center; margin-bottom: 40px;">
        <a href="mailto:{{ $data['email'] }}" style="display: inline-block; height: 56px; line-height: 56px; padding-left: 40px; padding-right: 40px; border-radius: 12px; background-color: #063654; color: #ffffff; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; font-size: 12px; border: none; text-decoration: none; box-shadow: 0 20px 25px -5px rgba(6, 54, 84, 0.2);">
            Reply to Message
        </a>
    </div>
@endsection
