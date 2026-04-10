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
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            word-break: break-word;
            -webkit-font-smoothing: antialiased;
            background-color: #eaeaea;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #eaeaea;
            padding-bottom: 60px;
        }

        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 650px;
            border-spacing: 0;
            font-family: 'Roboto', sans-serif;
            color: #333333;
            margin-top: 40px;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .header {
            background: linear-gradient(135deg, #FF6B6B, #C0392B);
            padding: 40px 40px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            font-size: 26px;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header img {
            max-width: 160px;
            max-height: 60px;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));
        }

        .content {
            padding: 50px 45px 35px 45px;
            font-size: 16px;
            line-height: 1.8;
            color: #555555;
        }

        /* Generic tags style overrides */
        .content h1, .content h2, .content h3 { color: #111; font-weight: 700; }
        .content a { color: #FF6B6B; font-weight: bold; text-decoration: none; }

        .footer {
            background-color: #222222;
            padding: 40px;
            text-align: center;
            font-size: 14px;
            color: #aaaaaa;
        }

        .social-links {
            margin-bottom: 25px;
        }

        .social-links a {
            display: inline-block;
            margin: 0 8px;
            text-decoration: none;
            background: #333333;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            line-height: 46px;
        }

        .social-links img {
            width: 18px;
            height: 18px;
            filter: grayscale(100%) brightness(300%);
            vertical-align: middle;
        }

        .footer p { margin: 0 0 10px 0; }
        .footer a { color: #FF6B6B; text-decoration: none; }
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
