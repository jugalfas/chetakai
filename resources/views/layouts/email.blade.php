<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    @yield('css')
</head>
<body style="background-color: #f1f5f9; font-family: 'Josefin Sans', ui-sans-serif, system-ui, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; margin: 0; padding: 0; color: #0f172a; height: 100vh; line-height: 1.5;">
    <div style="color: #f8fafc; background-color: #111827; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
        <div style="background-color: #020C17; display: flex; align-items: center; justify-content: center; min-height: 600px; padding: 32px;">
            <div style="width: 100%; max-width: 600px; background-color: #041C32; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid rgba(255, 255, 255, 0.05); font-family: 'Josefin Sans', ui-sans-serif, system-ui, sans-serif;">
                <div style="background-color: #041C32; padding-top: 24px; padding-bottom: 24px; padding-left: 32px; padding-right: 32px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); display: flex; align-items: center; justify-content: center; gap: 12px;">
                    <div style="height: 32px; width: 32px; border-radius: 4px; background-color: #ECB365; display: flex; align-items: center; justify-content: center; color: #041C32; font-weight: 700; font-size: 18px;">C</div><span style="color: #ffffff; font-weight: 700; font-size: 20px; letter-spacing: 0.05em;">Chetak</span>
                </div>
                <div style="padding: 32px; background-color: rgba(6, 70, 99, 0.18);">
                    @yield('content')
                </div>
                <div style="background-color: #041C32; padding-top: 32px; padding-bottom: 32px; padding-left: 32px; padding-right: 32px; border-top: 1px solid rgba(255, 255, 255, 0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <div style="color: #94a3b8; font-size: 14px;">
                            <p style="margin: 0;">Thanks for choosing Chetak,</p>
                            <p style="color: #ffffff; font-weight: 700; margin-top: 4px;">The Chetak Team</p>
                        </div>
                        <!-- <div style="display: flex; gap: 12px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 20px; width: 20px; color: #94a3b8;" aria-hidden="true">
                                <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                            </svg><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 20px; width: 20px; color: #94a3b8;" aria-hidden="true">
                                <path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"></path>
                            </svg></div> -->
                    </div>
                    <div style="text-align: center; font-size: 12px; color: #94a3b8; margin-top: 8px; text-transform: uppercase; letter-spacing: 0.05em;">
                        <p>© 2026 CHETAK AUTOMATION INC.</p>
                        <div style="display: flex; justify-content: center; gap: 16px;">
                            <a href="#" style="text-decoration: none; color: #cbd5e1;">Privacy</a>
                            <a href="#" style="text-decoration: none; color: #cbd5e1;">Terms</a>
                            <a href="#" style="text-decoration: none; color: #cbd5e1;">Unsubscribe</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
