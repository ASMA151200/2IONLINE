<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Resultat;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Aucune nouvelle table nécessaire — tout est calculé à partir des
 * données déjà en base (formations, inscriptions, paiements, resultats,
 * users).
 */
class AnalyticsController extends Controller
{
    /**
     * Résout un éventuel filtre ?month=YYYY-MM en bornes de dates
     * [début du mois, fin du mois] — ou [null, null] si absent (aucun
     * filtre, comportement global inchangé pour ne rien casser des
     * usages existants qui n'envoient pas ce paramètre).
     */
    private function resolveMonthRange(Request $request): array
    {
        $month = $request->query('month');

        if (!$month || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            return [null, null];
        }

        $debut = Carbon::createFromFormat('Y-m-d', "{$month}-01")->startOfDay();
        $fin = $debut->copy()->endOfMonth()->endOfDay();

        return [$debut, $fin];
    }

    /**
     * Vue d'ensemble globale (admin) — GET /v1/analytics/admin
     * Accepte ?month=YYYY-MM pour restreindre aux revenus/inscriptions/
     * résultats de CE mois précis (comportement global si omis).
     */
    public function admin(Request $request): JsonResponse
    {
        [$debut, $fin] = $this->resolveMonthRange($request);

        $totalStudents = User::where('role', 'etudiant')
            ->when($debut, fn ($q) => $q->whereBetween('created_at', [$debut, $fin]))
            ->count();

        $totalRevenue = Paiement::where('statut', 'confirme')
            ->when($debut, fn ($q) => $q->whereBetween('created_at', [$debut, $fin]))
            ->sum('montant');

        $totalEnrollments = Inscription::when($debut, fn ($q) => $q->whereBetween('created_at', [$debut, $fin]))->count();

        // "Actifs" n'a pas de définition naturelle "sur un mois" sans
        // journal d'activité dédié — reste un chiffre global même en
        // présence d'un filtre, pour éviter d'inventer une métrique
        // trompeuse.
        $activeUsers = User::where('is_active', true)->count();

        $resultatsQuery = Resultat::when($debut, fn ($q) => $q->whereBetween('date_passage', [$debut, $fin]));
        $totalResultats = (clone $resultatsQuery)->count();
        $completedResultats = (clone $resultatsQuery)->whereIn('statut', ['reussi', 'echoue'])->count();
        $completionRate = $totalResultats > 0 ? round(($completedResultats / $totalResultats) * 100, 1) : 0;
        $averageScore = round((float) (clone $resultatsQuery)->avg('score'), 1);

        return response()->json([
            'success' => true,
            'data' => [
                'totalStudents' => $totalStudents,
                'totalRevenue' => (float) $totalRevenue,
                'totalEnrollments' => $totalEnrollments,
                'activeUsers' => $activeUsers,
                'completionRate' => $completionRate,
                'averageScore' => $averageScore,
            ],
        ]);
    }

    /**
     * Analytics d'une formation précise — GET /v1/analytics/formations/{formation}
     */
    public function formation(Formation $formation): JsonResponse
    {
        $enrolledStudents = Inscription::where('formation_id', $formation->id)->count();
        $completedStudents = Inscription::where('formation_id', $formation->id)
            ->where('statut', 'termine')->count();
        $revenue = Paiement::where('formation_id', $formation->id)
            ->where('statut', 'confirme')->sum('montant');
        $averageScore = round((float) Resultat::whereHas('examen', function ($q) use ($formation) {
            $q->where('formation_id', $formation->id);
        })->avg('score'), 1);

        return response()->json([
            'success' => true,
            'data' => [
                'formationId' => (string) $formation->id,
                'name' => $formation->titre,
                'enrolledStudents' => $enrolledStudents,
                'completedStudents' => $completedStudents,
                'averageScore' => $averageScore,
                'revenue' => (float) $revenue,
                'completionRate' => $enrolledStudents > 0 ? round(($completedStudents / $enrolledStudents) * 100, 1) : 0,
            ],
        ]);
    }

    /**
     * Analytics de toutes les formations — GET /v1/analytics/formations
     * Accepte ?month=YYYY-MM (mêmes bornes que admin() ci-dessus) pour
     * ne compter que les inscriptions/paiements de ce mois précis.
     */
    public function allFormations(Request $request): JsonResponse
    {
        [$debut, $fin] = $this->resolveMonthRange($request);

        $formations = Formation::all();

        $data = $formations->map(function ($formation) use ($debut, $fin) {
            $enrolledStudents = Inscription::where('formation_id', $formation->id)
                ->when($debut, fn ($q) => $q->whereBetween('created_at', [$debut, $fin]))
                ->count();
            $completedStudents = Inscription::where('formation_id', $formation->id)
                ->where('statut', 'termine')
                ->when($debut, fn ($q) => $q->whereBetween('created_at', [$debut, $fin]))
                ->count();
            $revenue = Paiement::where('formation_id', $formation->id)
                ->where('statut', 'confirme')
                ->when($debut, fn ($q) => $q->whereBetween('created_at', [$debut, $fin]))
                ->sum('montant');

            return [
                'formationId' => (string) $formation->id,
                'name' => $formation->titre,
                'enrolledStudents' => $enrolledStudents,
                'completedStudents' => $completedStudents,
                'averageScore' => 0,
                'revenue' => (float) $revenue,
                'completionRate' => $enrolledStudents > 0 ? round(($completedStudents / $enrolledStudents) * 100, 1) : 0,
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Analytics par étudiant — GET /v1/analytics/students?student_id=...
     * (optionnel: tous les étudiants si student_id omis)
     */
    public function students(Request $request): JsonResponse
    {
        $query = User::where('role', 'etudiant');

        if ($request->filled('student_id')) {
            $query->where('id', $request->input('student_id'));
        }

        $students = $query->get();

        $data = $students->map(function ($student) {
            $enrollmentsCount = Inscription::where('user_id', $student->id)->count();
            $completedCourses = Inscription::where('user_id', $student->id)
                ->where('statut', 'termine')->count();
            $averageScore = round((float) Resultat::where('user_id', $student->id)->avg('score'), 1);

            return [
                'studentId' => (string) $student->id,
                'firstName' => $student->prenom,
                'lastName' => $student->nom,
                'email' => $student->email,
                'enrollmentsCount' => $enrollmentsCount,
                'completedCourses' => $completedCourses,
                'averageScore' => $averageScore,
                'lastActivity' => $student->updated_at?->toIso8601String(),
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Export PDF du rapport analytique (admin/formateur) — GET
     * /v1/analytics/export-pdf, accepte ?month=YYYY-MM comme les
     * endpoints ci-dessus. Pensé pour être partagé hors plateforme (par
     * email à un partenaire, par exemple) — d'où un PDF autonome plutôt
     * qu'un lien nécessitant une connexion.
     */
    public function exportPdf(Request $request)
    {
        [$debut, $fin] = $this->resolveMonthRange($request);
        $month = $request->query('month');

        $adminData = json_decode($this->admin($request)->getContent(), true)['data'];
        $formationsData = json_decode($this->allFormations($request)->getContent(), true)['data'];

        $pdf = app('dompdf.wrapper')->loadView('rapports.analytics', [
            'periode' => $month ? $debut->translatedFormat('F Y') : 'Depuis le lancement',
            'genereLe' => now()->translatedFormat('d/m/Y à H:i'),
            'stats' => $adminData,
            'formations' => $formationsData,
        ]);

        $nomFichier = 'rapport-analytics-' . ($month ?? 'global') . '.pdf';

        return $pdf->download($nomFichier);
    }

    /**
     * Chiffres clés PUBLICS pour la page "Impact" du site vitrine —
     * AUCUNE authentification requise. Volontairement limité à des
     * agrégats non sensibles (pas de noms, pas de montants détaillés par
     * formation, pas de données individuelles).
     */
    public function impact(): JsonResponse
    {
        $totalDiplomes = \App\Models\Certificat::count();
        $totalFormations = Formation::count();
        $totalEtudiants = User::where('role', 'etudiant')->count();
        $totalPartenaires = \App\Models\Partenaire::count();
        $totalInvesti = \Illuminate\Support\Facades\DB::table('formation_partenaire')->sum('montant_finance');
        $totalDons = \App\Models\Don::where('statut', 'confirme')->sum('montant');

        return response()->json([
            'success' => true,
            'data' => [
                'totalDiplomes' => $totalDiplomes,
                'totalFormations' => $totalFormations,
                'totalEtudiants' => $totalEtudiants,
                'totalPartenaires' => $totalPartenaires,
                'totalInvesti' => (float) $totalInvesti,
                'totalDons' => (float) $totalDons,
            ],
        ]);
    }
}
