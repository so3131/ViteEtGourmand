<?php
namespace App\Models;
use App\Config\Constants;
class Order
{


    public function __construct(
        public ?int $commande_id = null,
        public string $numero_commande = '',
        public string $date_commande = '',
        public string $date_prestation = '',
        public string $heure_livraison = '',
        public float $prix_menu = 0.0,
        public int $nombre_personne = 0,
        public float $prix_livraison = 0.0,
        public float $prix_total = 0.0,
        
        public string $statut = 'en_attente',
        public string $pret_materiel = 'non',
        public float $depot_garantie = 0.0,
        public string $restitution_materiel = '',
        public int $utilisateur_id = 0,
        public int $menu_id = 0,
        public int $lieu_prestation_id = 0,
        public int $nombre_personne_min = 0,
        public ?int $id = null,
        public string $nom = '',
        public string $ville = '',
        public float $km = 0.0,
    ) {}

public static function calculerFraisLivraisonParKm(string $ville, float $distanceKm): float
    {
        // Si la ville est Bordeaux, la livraison est gratuite
        if (mb_strtolower(trim($ville)) === 'bordeaux') {
            return 0.00;
        }

        // Sinon : 5 euros de base + 0,59 € par kilomètre parcouru
        $fraisBase = 5.00;
        $tarifKm = 0.59;

        $fraisTotal = $fraisBase + ($distanceKm * $tarifKm);

        return round($fraisTotal, 2);
    }
public static function calculerDistanceRouteVersClient(float $clientLat, float $clientLon): float
    {
        return self::calculerDistanceRoute(COMPANY_LAT, COMPANY_LON, $clientLat, $clientLon);
    }

    public static function calculerDistanceRoute(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        
        $apiKey = getenv('OPENROUTESERVICE_API_KEY') ?: ''; 
        
        $url = "https://api.openrouteservice.org/v2/directions/driving-car?api_key={$apiKey}&start={$lon1},{$lat1}&end={$lon2},{$lat2}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $data = json_decode($response, true);
            
            if (isset($data['features'][0]['properties']['segments'][0]['distance'])) {
                $distanceMetres = $data['features'][0]['properties']['segments'][0]['distance'];
                return round($distanceMetres / 1000, 2);
            }
        }

        // Fallback à vol d'oiseau
        return self::calculerDistanceVolOiseau($lat1, $lon1, $lat2, $lon2);
    }

    public static function calculerDistanceVolOiseau($lat1, $lon1, $lat2, $lon2)
    {
        $rayonEarth = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $rayonEarth * $c;
    }
}