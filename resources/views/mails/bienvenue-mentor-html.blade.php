<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 20px;">


    <div style="text-align: center; margin-bottom: 25px;">
        <img src="{{ $message->embed(public_path('logo.png')) }}" alt="Logo DOCOS" width="150"
            style="border: 0; max-width: 100%; height: auto;">
    </div>

    <h2>Bienvenue {{ $mentor['prenom'] }} {{ $mentor['nom'] }}</h2>

    <p>Votre compte mentor a bien été créé au sein de l'entreprise <strong> Nextmux</strong>.<br> Votre identifiant :
        <strong>{{ $mentor['email'] }}</strong>.<br> Votre mot de passe :
        <strong>{{ $password }}</strong>.<br><em> Nous
            vous recommandons de le changer dès votre première connexion.</em>
    </p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ config('app.url') }}"
            style="background-color: #2d3748; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
            Accéder à la plateforme
        </a>
    </div>

    <p>Cordialement,<br>{{ config('app.name') }}</p>
</body>

</html>
