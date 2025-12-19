<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\Aula;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $mesAtual = Carbon::now();
        
        // Estatísticas do mês
        $alunosAtivos = Aluno::where('user_id', $user->id)
            ->where('ativo', true)
            ->count();
            
        $aulasRealizadas = Aula::where('user_id', $user->id)
            ->where('status', 'realizada')
            ->whereMonth('data_hora', $mesAtual->month)
            ->whereYear('data_hora', $mesAtual->year)
            ->count();
            
        $faltasAluno = Aula::where('user_id', $user->id)
            ->where('status', 'cancelada_aluno')
            ->whereMonth('data_hora', $mesAtual->month)
            ->whereYear('data_hora', $mesAtual->year)
            ->count();
            
        $faltasProfessor = Aula::where('user_id', $user->id)
            ->where('status', 'cancelada_professor')
            ->whereMonth('data_hora', $mesAtual->month)
            ->whereYear('data_hora', $mesAtual->year)
            ->count();
            
        $cargaHoraria = Aula::where('user_id', $user->id)
            ->where('status', 'realizada')
            ->whereMonth('data_hora', $mesAtual->month)
            ->whereYear('data_hora', $mesAtual->year)
            ->sum('duracao_minutos');
            
        // Próximas aulas (7 dias)
        $proximasAulas = Aula::where('user_id', $user->id)
            ->where('status', 'agendada')
            ->whereBetween('data_hora', [Carbon::now(), Carbon::now()->addDays(7)])
            ->with('aluno')
            ->orderBy('data_hora')
            ->get();
            
        // Aulas da semana atual para o calendário
        $aulasSemana = Aula::where('user_id', $user->id)
            ->whereBetween('data_hora', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->with('aluno')
            ->orderBy('data_hora')
            ->get();

        // Adicionar horários recorrentes dos alunos ao calendário
        $horariosRecorrentes = collect();
        $alunosComHorarios = Aluno::where('user_id', $user->id)
            ->where('ativo', true)
            ->with(['horariosAtivos'])
            ->get();

        $inicioSemana = Carbon::now()->startOfWeek();
        
        foreach ($alunosComHorarios as $aluno) {
            foreach ($aluno->horariosAtivos as $horario) {
                // Criar aula virtual para cada horário recorrente na semana
                $diaHorario = $inicioSemana->copy()->addDays($horario->dia_semana);
                
                // Verificar se não existe aula já cadastrada nesse horário
                $aulaExistente = $aulasSemana->first(function($aula) use ($aluno, $diaHorario, $horario) {
                    return $aula->aluno_id === $aluno->id 
                        && $aula->data_hora->isSameDay($diaHorario)
                        && $aula->data_hora->format('H:i') === $horario->hora_inicio;
                });

                if (!$aulaExistente) {
                    $aulaVirtual = new Aula([
                        'aluno_id' => $aluno->id,
                        'data_hora' => $diaHorario->copy()->setTimeFromTimeString($horario->hora_inicio),
                        'duracao_minutos' => $horario->duracao_minutos,
                        'status' => 'agendada',
                    ]);
                    $aulaVirtual->aluno = $aluno;
                    $aulaVirtual->is_recorrente = true;
                    $horariosRecorrentes->push($aulaVirtual);
                }
            }
        }

        // Combinar aulas reais com horários recorrentes
        $aulasSemana = $aulasSemana->merge($horariosRecorrentes)->sortBy('data_hora');
        
        return view('dashboard', compact(
            'alunosAtivos',
            'aulasRealizadas',
            'faltasAluno',
            'faltasProfessor',
            'cargaHoraria',
            'proximasAulas',
            'aulasSemana'
        ));
    }
}
