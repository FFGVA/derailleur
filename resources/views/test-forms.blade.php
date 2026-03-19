<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FFGVA – Formulaires de test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #f5f1e9; }
        .btn-primary { background-color: #80081C; }
        .btn-primary:hover { background-color: #660614; }
    </style>
</head>
<body class="min-h-screen py-12 px-4">
    <div class="max-w-4xl mx-auto space-y-12">

        <h1 class="text-3xl font-bold text-center" style="color: #80081C;">FFGVA – Formulaires de test</h1>

        {{-- Contact form --}}
        <div class="bg-white rounded-lg shadow p-8">
            <h2 class="text-2xl font-semibold mb-6" style="color: #80081C;">Formulaire de contact</h2>
            <div id="contact-message" class="hidden mb-4 p-4 rounded"></div>
            <form id="contact-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" name="name" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2" style="focus:ring-color: #80081C;">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea name="message" required rows="4" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2"></textarea>
                </div>
                <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
                <button type="submit" class="btn-primary text-white font-semibold px-6 py-2 rounded hover:opacity-90">Envoyer</button>
            </form>
        </div>

        {{-- Adhesion form --}}
        <div class="bg-white rounded-lg shadow p-8">
            <h2 class="text-2xl font-semibold mb-6" style="color: #80081C;">Formulaire d'adhésion</h2>
            <div id="adhesion-message" class="hidden mb-4 p-4 rounded"></div>
            <form id="adhesion-form" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                        <input type="text" name="nom" required class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                        <input type="text" name="prenom" required class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail *</label>
                        <input type="email" name="email" required class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                        <input type="tel" name="telephone" required class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Autorisation photo *</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="photo_ok" value="oui" required class="mr-2"> Oui
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="photo_ok" value="non" class="mr-2"> Non
                        </label>
                    </div>
                </div>

                <hr class="my-4 border-gray-200">
                <p class="text-sm text-gray-500 italic">Champs optionnels</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de vélo</label>
                        <input type="text" name="type_velo" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Route, Gravel, VTT...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sorties préférées</label>
                        <input type="text" name="sorties" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Weekend, semaine...">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Intérêt pour l'atelier mécanique</label>
                    <input type="text" name="atelier" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Oui, non, peut-être...">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
                        <input type="text" name="instagram" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="@votre_compte">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Strava</label>
                        <input type="text" name="strava" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Lien ou nom de profil">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="statuts_ok" value="1"> <span class="text-sm text-gray-700">J'ai lu et accepté les statuts de l'association</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="cotisation_ok" value="1"> <span class="text-sm text-gray-700">J'accepte de payer la cotisation annuelle</span>
                    </label>
                </div>

                <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
                <button type="submit" class="btn-primary text-white font-semibold px-6 py-2 rounded hover:opacity-90">Envoyer la demande</button>
            </form>
        </div>

    </div>

    <script>
        function showMessage(elId, text, success) {
            const el = document.getElementById(elId);
            el.textContent = text;
            el.className = 'mb-4 p-4 rounded ' + (success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
        }

        document.getElementById('contact-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const data = Object.fromEntries(new FormData(form));
            try {
                const res = await fetch('/api/contact', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(data),
                });
                if (res.ok) {
                    showMessage('contact-message', 'Message envoyé avec succès !', true);
                    form.reset();
                } else {
                    const err = await res.json();
                    showMessage('contact-message', 'Erreur : ' + (err.message || JSON.stringify(err.errors)), false);
                }
            } catch (ex) {
                showMessage('contact-message', 'Erreur réseau : ' + ex.message, false);
            }
        });

        document.getElementById('adhesion-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const data = {};
            formData.forEach((val, key) => { if (val) data[key] = val; });
            try {
                const res = await fetch('/api/adhesion', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(data),
                });
                if (res.ok) {
                    showMessage('adhesion-message', 'Demande d\'adhésion envoyée avec succès !', true);
                    form.reset();
                } else {
                    const err = await res.json();
                    showMessage('adhesion-message', 'Erreur : ' + (err.message || JSON.stringify(err.errors)), false);
                }
            } catch (ex) {
                showMessage('adhesion-message', 'Erreur réseau : ' + ex.message, false);
            }
        });
    </script>
</body>
</html>
