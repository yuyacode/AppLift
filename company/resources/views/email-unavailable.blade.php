<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>AppLift</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div>
            <h1>メール送信機能について</h1>
            <p>大変申し訳ございませんが、現在、このアプリケーションのメール送信機能はご利用いただけません。</p>
        
            <h2>なぜ現在利用できないのか？</h2>
        
            <h3>1. AWS SESの技術的な制約</h3>
            <p>本アプリケーションはAWS上で稼働しており、メール送信にはAWS Simple Email Service (SES) の利用を想定しています。AWS SESのアカウントは、初期状態では「サンドボックス環境」に設定されており、未認証のメールアドレスへのメール送信が制限されています。</p>
            <p>本番環境でユーザーの皆様に広くメールを送信するためには、AWSへのサンドボックス解除申請（本番アクセス申請）を行い、審査を通過する必要があります。この申請には一定の準備と時間を要します。</p>
        
            <h3>2. 開発リソースの戦略的配分</h3>
            <p>このアプリケーションは、私の転職活動におけるポートフォリオとして開発しています。限られた開発期間とリソースの中で、より評価に直結する技術要素や面接対策などに優先的に時間を投入しました。</p>
            <p>メール送信機能はユーザー体験を向上させる重要な要素ですが、現状ではアプリケーションの基本的な利用を妨げるものではありません。この判断は「転職成功」という最終目標に対する時間と労力の最適化を考慮した結果です。</p>
        
            <h2>将来的な展望</h2>
            <p>メール送信機能の実現は、本アプリケーションの完成度を高める上で不可欠であると認識しており、サンドボックス解除を含めた本格的なSES基盤の構築を、今後の対応事項の一つとして考えています。</p>
            <p>AWS CloudFormationを用いたSESリソースのIaC化、バウンス対応のためのSNS・SQS連携など、本番運用に耐えうる堅牢なメール送信基盤を構築する意欲と計画があります。</p>
        
            <p><a href="javascript:history.back()">前のページに戻る</a></p>
        </div>
    </body>
</html>
