@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1 class="fw-bold mb-5 text-center">Comment ça marche</h1>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Introduction -->
            <div class="card shadow-sm mb-5">
                <div class="card-body p-4">
                    <h2 class="card-title fs-4 fw-bold mb-3">Bienvenue sur Supreme Musique</h2>
                    <p class="lead">
                        Notre plateforme vous permet d'acheter des singles et des albums et de les écouter en streaming.
                        Contrairement aux services traditionnels, vous n'aurez pas à télécharger la musique, mais vous pourrez
                        y accéder depuis vos playlists personnalisées après l'achat.
                    </p>
                </div>
            </div>
            
            <!-- Étapes -->
            <div class="timeline">
                <!-- Étape 1 -->
                <div class="card shadow-sm mb-4 border-start border-primary border-5">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <h3 class="m-0 fw-bold">1</h3>
                            </div>
                            <h3 class="fs-4 fw-bold mb-0">Parcourez notre catalogue</h3>
                        </div>
                        <p>
                            Explorez notre vaste collection d'albums et de singles. Vous pouvez consulter tout notre catalogue sans avoir 
                            à créer un compte, ce qui vous permet de découvrir notre sélection avant de vous inscrire.
                        </p>
                        <a href="{{ route('catalog') }}" class="btn btn-outline-primary">Explorer le catalogue</a>
                    </div>
                </div>
                
                <!-- Étape 2 -->
                <div class="card shadow-sm mb-4 border-start border-success border-5">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <h3 class="m-0 fw-bold">2</h3>
                            </div>
                            <h3 class="fs-4 fw-bold mb-0">Créez un compte</h3>
                        </div>
                        <p>
                            Pour acheter de la musique et créer des playlists, vous devez créer un compte. 
                            L'inscription est gratuite et ne prend que quelques secondes. Une fois inscrit, 
                            vous pourrez commencer à acheter de la musique et à créer vos playlists personnalisées.
                        </p>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-outline-success">S'inscrire maintenant</a>
                        @else
                            <span class="badge bg-success">Vous êtes déjà inscrit !</span>
                        @endguest
                    </div>
                </div>
                
                <!-- Étape 3 -->
                <div class="card shadow-sm mb-4 border-start border-info border-5">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <h3 class="m-0 fw-bold">3</h3>
                            </div>
                            <h3 class="fs-4 fw-bold mb-0">Ajoutez au panier et achetez</h3>
                        </div>
                        <p>
                            Ajoutez vos albums et titres préférés à votre panier, puis procédez au paiement.
                            Nous proposons des paiements sécurisés via Orange Money, ce qui rend vos achats 
                            simples et sûrs.
                        </p>
                        @auth
                            <a href="{{ route('cart.show') }}" class="btn btn-outline-info">Voir mon panier</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-info">Se connecter pour acheter</a>
                        @endauth
                    </div>
                </div>
                
                <!-- Étape 4 -->
                <div class="card shadow-sm mb-4 border-start border-warning border-5">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <h3 class="m-0 fw-bold">4</h3>
                            </div>
                            <h3 class="fs-4 fw-bold mb-0">Créez vos playlists</h3>
                        </div>
                        <p>
                            Une fois vos achats effectués, vos titres et albums seront disponibles dans votre bibliothèque.
                            Vous pourrez créer des playlists personnalisées en organisant vos morceaux préférés selon vos goûts.
                        </p>
                        @auth
                            <a href="{{ route('playlists.index') }}" class="btn btn-outline-warning">Gérer mes playlists</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-warning">Se connecter pour créer des playlists</a>
                        @endauth
                    </div>
                </div>
                
                <!-- Étape 5 -->
                <div class="card shadow-sm mb-4 border-start border-danger border-5">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <h3 class="m-0 fw-bold">5</h3>
                            </div>
                            <h3 class="fs-4 fw-bold mb-0">Écoutez en streaming</h3>
                        </div>
                        <p>
                            Profitez de la musique que vous avez achetée en streaming de haute qualité.
                            Vous pouvez écouter vos titres préférés à tout moment et de n'importe où, sans avoir à télécharger de fichiers.
                            Notre lecteur vous permet de créer l'ambiance parfaite pour chaque occasion.
                        </p>
                        @auth
                            <a href="{{ route('library') }}" class="btn btn-outline-danger">Accéder à ma bibliothèque</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-danger">Se connecter pour écouter</a>
                        @endauth
                    </div>
                </div>
            </div>
            
            <!-- FAQ -->
            <div class="mt-5">
                <h2 class="fw-bold mb-4">Questions fréquentes</h2>
                
                <div class="accordion" id="faqAccordion">
                    <!-- Question 1 -->
                    <div class="accordion-item shadow-sm mb-3 border">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                Puis-je télécharger la musique que j'achète ?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Non, notre modèle est basé sur le streaming. Vous n'avez pas besoin de télécharger les fichiers 
                                audio pour les écouter. Une fois que vous avez acheté un titre ou un album, vous pouvez y accéder 
                                en streaming à tout moment depuis votre bibliothèque ou vos playlists. Cela vous permet d'économiser 
                                de l'espace de stockage sur vos appareils tout en profitant d'une haute qualité audio.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Question 2 -->
                    <div class="accordion-item shadow-sm mb-3 border">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Comment fonctionne le paiement par Orange Money ?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Lors du paiement, vous renseignez votre numéro Orange Money. Vous recevrez ensuite une notification 
                                sur votre téléphone pour valider le paiement. Une fois la transaction confirmée, vous aurez 
                                immédiatement accès à vos achats. Le processus est sécurisé et ne prend que quelques secondes.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Question 3 -->
                    <div class="accordion-item shadow-sm mb-3 border">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Puis-je écouter ma musique hors connexion ?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Actuellement, nous ne proposons pas d'écoute hors ligne. Vous avez besoin d'une connexion internet 
                                pour accéder à votre musique. Cependant, nous travaillons sur une fonctionnalité future qui 
                                permettra l'écoute hors ligne pour une durée limitée.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Question 4 -->
                    <div class="accordion-item shadow-sm mb-3 border">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                Est-ce que je dois payer un abonnement mensuel ?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Non, nous ne proposons pas d'abonnement mensuel. Vous ne payez que pour la musique que vous achetez.
                                Une fois que vous avez acheté un titre ou un album, vous pouvez l'écouter autant de fois que vous 
                                le souhaitez sans frais supplémentaires.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Assistance -->
            <div class="card shadow-sm mt-5">
                <div class="card-body p-4 text-center">
                    <h2 class="card-title fs-4 fw-bold mb-3">Besoin d'aide supplémentaire ?</h2>
                    <p>Notre équipe est disponible pour répondre à toutes vos questions.</p>
                    <a href="{{ route('contact') }}" class="btn btn-primary">Nous contacter</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .timeline .card {
        position: relative;
        transition: transform 0.3s;
    }
    
    .timeline .card:hover {
        transform: translateX(10px);
    }
</style>
@endsection
