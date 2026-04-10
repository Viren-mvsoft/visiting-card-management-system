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
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap');

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            word-break: break-word;
            -webkit-font-smoothing: antialiased;
            background-color: #0f172a;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #0f172a;
            padding-bottom: 60px;
        }

        .main {
            background-color: #1e293b;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-spacing: 0;
            font-family: 'Outfit', sans-serif;
            color: #f1f5f9;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.25);
            margin-top: 40px;
            border: 1px solid #334155;
        }

        .header {
            padding: 40px 40px 20px 40px;
            text-align: left;
        }

        .header h1 {
            color: #f8fafc;
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }

        .header img {
            max-width: 140px;
            max-height: 50px;
        }

        .content {
            padding: 20px 40px 40px 40px;
            font-size: 16px;
            line-height: 1.7;
            color: #cbd5e1;
            font-weight: 300;
        }

        /* Standardize user content */
        .content h1, .content h2, .content h3 { color: #f8fafc; }
        .content a { color: #38bdf8; text-decoration: none; }

        .footer {
            padding: 30px 40px 40px;
            border-top: 1px solid #334155;
            font-size: 13px;
            color: #64748b;
        }

        .social-links {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .social-links a {
            display: inline-block;
            margin-right: 15px;
            text-decoration: none;
            opacity: 0.6;
        }

        .social-links img {
            width: 20px;
            height: 20px;
            filter: grayscale(100%) brightness(200%);
        }

        .footer p { margin: 0 0 10px 0; }
        .footer a { color: #94a3b8; text-decoration: none; }
    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main" width="100%">
            <!-- Header section -->
            <tr>
                <td class="header">
                    @if(!empty($settings['company_logo']))
                        <img src="{{ url(Storage::url($settings['company_logo'])) }}" alt="{{ $settings['company_name'] ?? 'Company Logo' }}" style="border: 0;">
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
