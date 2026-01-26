<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .email-header {
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body h2 {
            color: #333;
            font-size: 22px;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .email-body p {
            color: #666;
            font-size: 16px;
            margin: 16px 0;
        }
        .reset-button {
            display: inline-block;
            margin: 30px 0;
            padding: 14px 40px;
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s;
        }
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.4);
        }
        .button-container {
            text-align: center;
        }
        .security-notice {
            background-color: #FFF9E6;
            border-left: 4px solid #FFD700;
            padding: 15px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .security-notice p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
        .alternative-link {
            background-color: #f8f9fa;
            padding: 20px;
            margin: 25px 0;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
        .alternative-link p {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
        }
        .alternative-link code {
            display: block;
            background-color: #fff;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            word-break: break-all;
            font-size: 13px;
            color: #495057;
            margin-top: 8px;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        .email-footer p {
            margin: 8px 0;
            color: #6c757d;
            font-size: 14px;
        }
        .email-footer a {
            color: #4A90E2;
            text-decoration: none;
        }
        .logo {
            font-size: 16px;
            color: #ffffff;
            opacity: 0.9;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🌟 FundTastic</h1>
            <p class="logo">Crowdfunding Platform</p>
        </div>
        
        <div class="email-body">
            <h2>Hi {{ $userName }},</h2>
            
            <p>We received a request to reset the password for your FundTastic account.</p>
            
            <p>Click the button below to choose a new password:</p>
            
            <div class="button-container">
                <a href="{{ $resetUrl }}" class="reset-button">Reset Password</a>
            </div>
            
            <div class="security-notice">
                <p><strong>⏰ This link expires in 60 minutes</strong> for your security.</p>
            </div>
            
            <div class="alternative-link">
                <p><strong>Button not working?</strong> Copy and paste this URL into your browser:</p>
                <code>{{ $resetUrl }}</code>
            </div>
            
            <p>If you didn't request a password reset, you can safely ignore this email. Your password will remain unchanged.</p>
            
            <p>For security reasons, this link can only be used once.</p>
        </div>
        
        <div class="email-footer">
            <p><strong>FundTastic Team</strong></p>
            <p>Making crowdfunding accessible to everyone</p>
            <p style="margin-top: 15px;">
                <a href="{{ env('APP_URL', 'http://localhost:8000') }}">Visit FundTastic</a>
            </p>
            <p style="font-size: 12px; color: #999; margin-top: 15px;">
                This is an automated message, please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
