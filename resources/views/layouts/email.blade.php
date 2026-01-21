<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body style="background-color: #f1f5f9; font-family: 'Josefin Sans', ui-sans-serif, system-ui, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; margin: 0; padding: 0; color: #0f172a;">
    <div style="min-height: 100vh; background-color: #041C32; display: flex; align-items: center; justify-content: center; padding: 16px;">
        <div style="width: 100%; max-width: 672px; background-color: #064663; border-radius: 32px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden; font-family: 'Josefin Sans', ui-sans-serif, system-ui, sans-serif;">
            <div style="padding: 32px; text-align: center; border-bottom: 1px solid rgba(255, 255, 255, 0.05); background-color: rgba(0, 0, 0, 0.2);">
                <div style="display: inline-flex; align-items: center; gap: 12px;">
                    <div style="height: 40px; width: 40px; border-radius: 12px; background-color: #EBB066; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 15px -3px rgba(235, 176, 102, 0.2);"><span style="color: #ffffff; font-weight: 700; font-size: 20px;">C</span></div>
                    <h2 style="font-size: 24px; font-weight: 700; color: #ffffff; letter-spacing: -0.025em; font-family: 'Josefin Sans', sans-serif; margin: 0;">Chetak</h2>
                </div>
            </div>
            <div style="padding: 32px;">
                @yield('content')
                <div style="color: #94a3b8; padding-top: 32px; border-top: 1px solid rgba(255, 255, 255, 0.05); display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;">
                    <div>
                        <p style="font-size: 12px; margin: 0;">Thanks for choosing Chetak,</p>
                        <p style="font-weight: 700; color: #ffffff; font-family: 'Josefin Sans', sans-serif; margin: 5px 0;">The Chetak Team</p>
                    </div>
                    <div style="display: flex; gap: 12px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-instagram h-5 w-5 text-slate-400" aria-hidden="true">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                        </svg><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-zap h-5 w-5 text-slate-400" aria-hidden="true">
                            <path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"></path>
                        </svg></div>
                </div>
            </div>
            <div style="background-color: rgba(0, 0, 0, 0.2); padding: 32px; text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.05);">
                <p style="font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 0.2em; margin: 0 0 16px 0;">© 2026 Chetak Automation Inc.</p>
                <div style="display: flex; justify-content: center; gap: 24px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #475569;"><a href="#" style="color: inherit; text-decoration: none;">Privacy</a><a href="#" style="color: inherit; text-decoration: none;">Terms</a><a href="#" style="color: inherit; text-decoration: none;">Unsubscribe</a></div>
            </div>
        </div>
    </div>
</body>
</html>
