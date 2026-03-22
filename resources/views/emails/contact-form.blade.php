@extends('layouts.email')

@section('css')
<style>
  .email-btn:hover {
    background-color: #020C17 !important;
  }
</style>
@endsection

@section('content')
<div style="display: inline-flex; align-items: center; gap: 8px; background-color: rgba(236, 179, 101, 0.1); color: #ECB365; padding-left: 12px; padding-right: 12px; padding-top: 4px; padding-bottom: 4px; border-radius: 9999px; font-size: 12px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;"><span style="height: 6px; width: 6px; border-radius: 9999px; background-color: #ECB365; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></span>New Submission</div>
<div style="margin-top: 24px;">
    <h1 style="font-size: 24px; font-weight: 700; color: #ffffff; margin-bottom: 12px;">You've got a new lead waiting!</h1>
    <p style="color: #cbd5e1; font-size: 14px; line-height: 1.625;">A potential user just reached out through your website contact form. Here are the details:</p>
</div>
<div style="background-color: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 24px; margin-top: 20px;">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
        <div style="height: 40px; width: 40px; border-radius: 8px; background-color: rgba(255, 255, 255, 0.05); display: flex; align-items: center; justify-content: center; color: #94a3b8;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 20px; width: 20px;" aria-hidden="true">
                <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path>
                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
            </svg></div>
        <div>
            <p style="font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin: 0px; margin-bottom: 2px">From</p>
            <p style="font-size: 14px; font-weight: 500; color: #ffffff;  margin: 0px;">{{ $data['name'] }} <span style="color: #94a3b8; font-weight: 400;">&lt;{{ $data['email'] }}&gt;</span></p>
        </div>
    </div>
    <div style="padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.05);">
        <p style="font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin-bottom: 12px;">Message</p>
        <!-- <div style="color: #cbd5e1; font-size: 14px; font-style: italic; border-left: 2px solid rgba(236, 179, 101, 0.3); padding-left: 16px; margin-top: 16px;">
            <p>"Hello!</p>
            <p>This is a preview of the new contact form email template. It supports multiple lines and uses the custom layout we just created.</p>
            <p>Best regards,<br>Chetak Team"</p>
        </div> -->
        <div style="color: #cbd5e1; line-height: 1.625; font-size: 14px; font-style: italic; border-left: 2px solid rgba(236, 179, 101, 0.3); padding-left: 16px; padding-top: 4px; padding-bottom: 4px; white-space: pre-wrap;">"{{ $data['message'] }}"</div>
    </div>
</div>
<div style="padding-top: 16px; display: flex; justify-content: center;"><button class="email-btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; white-space: nowrap; transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; min-height: 36px; background-color: #041C32; color: #ffffff; border: 1px solid #ffffff1a; padding: 16px; border-radius: 14px; font-size: 14px; letter-spacing: 0.1em; text-transform: uppercase; font-weight: 700; width: auto;font-family: 'Josefin Sans', ui-sans-serif, system-ui, sans-serif; ">Reply to Message</button></div>
@endsection
