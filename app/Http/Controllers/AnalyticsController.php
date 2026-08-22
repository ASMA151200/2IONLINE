<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Resultat;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Aucune nouvelle table nécessaire — tout est calculé à partir des
 * données déjà en base (formations, inscriptions, paiements, resultats,
 * users).
 */
class AnalyticsController extends Controller
{
    /**
     * Vue d'ensemble globale (admin) — GET /v1/analytics/admin
     */
    public function admin(): JsonResponse
    {
        $totalStudents = User::where('role', 'etudiant')->count();
        $totalRevenue = Paiement::where('statut', 'confirme')->sum('montant');
        $totalEnrollments = Inscription::count();
        $activeUsers = User::where('is_active', true)->count();

        $totalResultats = Resultat::count();
        $completedResultats = Resultat::whereIn('statut', ['reussi', 'echoue'])->count();
        $completionRate = $totalResultats > 0 ? round(($completedResultats / $totalResultats) * 100, 1) : 0;
        $averageScore = round((float) Resultat::avg('score'), 1);

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
     */
    public function allFormations(): JsonResponse
    {
        $formations = Formation::all();

        $data = $formations->map(function ($formation) {
            $enrolledStudents = Inscription::where('formation_id', $formation->id)->count();
            $completedStudents = Inscription::where('formation_id', $formation->id)
                ->where('statut', 'termine')->count();
            $revenue = Paiement::where('formation_id', $formation->id)
                ->where('statut', 'confirme')->sum('montant');

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
}
