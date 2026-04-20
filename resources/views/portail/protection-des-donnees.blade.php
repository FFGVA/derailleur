@extends('portail.layout')

@section('title', 'Protection des données')

@section('styles')
    .portal-lpd-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .portal-lpd-card h1 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #80081C;
        margin-bottom: 1.25rem;
    }
    .portal-lpd-card h2 {
        font-size: 1rem;
        font-weight: 700;
        color: #333;
        margin: 1.25rem 0 0.5rem;
    }
    .portal-lpd-card p, .portal-lpd-card li {
        font-size: 0.9375rem;
        line-height: 1.6;
        color: #444;
        margin-bottom: 0.625rem;
    }
    .portal-lpd-card ul {
        padding-left: 1.25rem;
        margin-bottom: 0.625rem;
    }
    .portal-lpd-card li {
        margin-bottom: 0.25rem;
    }
    .portal-lpd-contact {
        margin-top: 1.25rem;
        padding: 1rem;
        background-color: #f5f1e9;
        border-radius: 0.5rem;
        font-size: 0.9375rem;
        line-height: 1.6;
        color: #444;
    }
@endsection

@section('header')
    <header class="portal-header">
        <span class="portal-brand">Protection des données</span>
        <a href="{{ route('portail.adhesion') }}" class="portal-header-action">Retour</a>
    </header>
@endsection

@section('content')
    <div class="portal-lpd-card">
        <h1>Déclaration de protection des données</h1>

        <p>Fast and Female Geneva (ci-après « l'association ») traite les données personnelles de ses membres conformément à la Loi fédérale sur la protection des données (LPD) et aux principes de proportionnalité et de transparence.</p>

        <h2>Responsable du traitement</h2>
        <p>Fast and Female Geneva<br>
        {{ config('ffgva.creditor_postal_code') }} {{ config('ffgva.creditor_city') }}</p>

        <h2>Préposée à la protection des données</h2>
        <p>La présidente de Fast and Female Geneva assume la fonction de préposée à la protection des données.<br>
        Contact : <a href="mailto:{{ config('ffgva.contact_email') }}" style="color: #80081C;">{{ config('ffgva.contact_email') }}</a></p>

        <h2>Données collectées</h2>
        <p>L'association collecte et traite les données suivantes :</p>
        <ul>
            <li>Identité : prénom, nom, date de naissance</li>
            <li>Coordonnées : adresse email, numéros de téléphone, adresse postale</li>
            <li>Adhésion : numéro de membre, statut, dates d'adhésion</li>
            <li>Activités : inscriptions aux événements, présences</li>
            <li>Informations optionnelles : type de vélo, préférences de sorties, comptes Instagram et Strava</li>
            <li>Données financières : factures et statut de paiement</li>
        </ul>

        <h2>Finalité du traitement</h2>
        <p>Les données personnelles sont utilisées exclusivement dans le cadre de la poursuite des objectifs de l'association :</p>
        <ul>
            <li>Gestion des adhésions et des cotisations</li>
            <li>Organisation des sorties et événements</li>
            <li>Communication avec les membres (emails transactionnels)</li>
            <li>Établissement des factures et suivi des paiements</li>
        </ul>

        <h2>Base juridique</h2>
        <p>Le traitement repose sur le consentement de la membre, donné lors de la confirmation de son inscription. Ce consentement peut être retiré à tout moment par demande écrite au comité.</p>

        <h2>Transmission à des tiers</h2>
        <p>Pour les membres actives, l'association peut transmettre le nom et les informations de membre à des partenaires tiers afin de faire bénéficier les membres de leurs avantages (réductions sur des produits, offres spéciales). Cette transmission se fait dans l'intérêt des membres et dans le cadre des objectifs de l'association.</p>
        <p>Aucun outil d'analyse, de tracking ou de publicité n'est utilisé sur la plateforme.</p>

        <h2>Réseaux sociaux</h2>
        <p>L'association peut identifier ses membres par leur nom dans ses publications sur les réseaux sociaux. Si un compte Instagram ou Strava a été communiqué, l'association peut également taguer la membre dans ses publications.</p>

        <h2>Durée de conservation</h2>
        <p>Les données des membres actives sont conservées pendant toute la durée de l'adhésion. Après la fin de l'adhésion, les données personnelles sont conservées pendant une durée maximale de 3 ans pour des raisons organisationnelles, puis supprimées. Les données financières (factures, paiements) sont conservées pendant 5 ans conformément aux obligations légales suisses en matière de comptabilité.</p>

        <h2>Sécurité des données</h2>
        <p>L'association met en œuvre les mesures techniques et organisationnelles appropriées pour protéger les données personnelles contre tout accès non autorisé, perte ou altération. L'accès aux données est limité aux membres du comité et aux cheffes de peloton dans le cadre de leurs responsabilités.</p>

        <h2>Droits des membres</h2>
        <p>Conformément à la LPD, chaque membre dispose des droits suivants :</p>
        <ul>
            <li><strong>Droit d'accès</strong> — obtenir une copie de ses données personnelles</li>
            <li><strong>Droit de rectification</strong> — demander la correction de données inexactes</li>
            <li><strong>Droit d'effacement</strong> — demander la suppression de ses données</li>
            <li><strong>Droit de retrait du consentement</strong> — retirer son consentement à tout moment</li>
        </ul>
        <p>Ces droits peuvent être exercés par email à l'adresse ci-dessous.</p>

        <h2>Cookies</h2>
        <p>La page d'accueil du site n'utilise aucun cookie. L'espace membre utilise un cookie de session strictement nécessaire au fonctionnement de l'authentification. Aucun cookie tiers, de tracking ou publicitaire n'est utilisé.</p>

        <div class="portal-lpd-contact">
            <strong>Contact</strong><br>
            Pour toute question relative à la protection de tes données personnelles :<br>
            <a href="mailto:{{ config('ffgva.contact_email') }}" style="color: #80081C;">{{ config('ffgva.contact_email') }}</a>
        </div>
    </div>
@endsection
