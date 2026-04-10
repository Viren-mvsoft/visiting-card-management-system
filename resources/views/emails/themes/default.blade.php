<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <title>Email</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            word-break: break-word;
            -webkit-font-smoothing: antialiased;
            background-color: #f3f4f6;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f3f4f6;
            padding-bottom: 60px;
        }

        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-spacing: 0;
            font-family: 'Inter', sans-serif;
            color: #1f2937;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin-top: 40px;
        }

        .header {
            background-color: #ffffff;
            padding: 30px 40px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.5px;
        }

        .content {
            padding: 40px;
            background-color: #ffffff;
            font-size: 16px;
            line-height: 1.6;
            color: #4b5563;
        }

        .footer {
            background-color: #f9fafb;
            padding: 30px 40px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }

        .social-links {
            margin-bottom: 20px;
        }

        .social-links a {
            display: inline-block;
            margin: 0 10px;
            text-decoration: none;
        }

        .social-links img {
            width: 24px;
            height: 24px;
            opacity: 0.7;
        }

        .footer p {
            margin: 0 0 10px 0;
        }

        .footer a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main" width="100%">
            <!-- Header section -->
            <tr>
                <td class="header">
                    @if(!empty($settings['company_logo']))
                        <img src="{{ url(Storage::url($settings['company_logo'])) }}" alt="{{ $settings['company_name'] ?? 'Company Logo' }}" style="max-width: 150px; max-height: 50px; border: 0;">
                    @elseif(!empty($settings['company_name']))
                        <h1>{{ $settings['company_name'] }}</h1>
                    @endif
                </td>
            </tr>

            <!-- Body section -->
            <tr>
                <td class="content">
                    {!! $body !!}
                </td>
            </tr>

            <!-- Footer section -->
            <tr>
                <td class="footer">
                    <div class="social-links">
                        @if(!empty($settings['facebook_link']))
                            <a href="{{ $settings['facebook_link'] }}"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook"></a>
                        @endif
                        @if(!empty($settings['twitter_link']))
                            <a href="{{ $settings['twitter_link'] }}"><img src="https://cdn-icons-png.flaticon.com/512/733/733590.png" alt="Twitter"></a>
                        @endif
                        @if(!empty($settings['linkedin_link']))
                            <a href="{{ $settings['linkedin_link'] }}"><img src="https://cdn-icons-png.flaticon.com/512/733/733561.png" alt="LinkedIn"></a>
                        @endif
                        @if(!empty($settings['instagram_link']))
                            <a href="{{ $settings['instagram_link'] }}"><img src="https://cdn-icons-png.flaticon.com/512/733/733558.png" alt="Instagram"></a>
                        @endif
                    </div>
                    
                    <p>&copy; {{ date('Y') }} {{ $settings['company_name'] ?? 'Company Name' }}. All rights reserved.</p>
                    
                    @if(!empty($settings['website_link']))
                        <p>Visit us at <a href="{{ $settings['website_link'] }}">{{  parse_url($settings['website_link'], PHP_URL_HOST) ?? $settings['website_link'] }}</a></p>
                    @endif
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
