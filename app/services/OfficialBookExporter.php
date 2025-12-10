<?php
require_once __DIR__ . '/SimplePdf.php';

class OfficialBookExporter
{
    public function pdf(array $institution, array $championship, array $players, array $groups, array $matches): string
    {
        $pdf = new SimplePdf();
        $pdf->addLine($institution['name'] ?? 'Institución organizadora');
        $pdf->addLine('Libro oficial de campeonato');
        $pdf->addSpacer();
        $pdf->addLine('Campeonato: ' . ($championship['name'] ?? '')); 
        $pdf->addLine('Fecha: ' . ($championship['start_date'] ?? ''));
        $pdf->addLine('Ubicación: ' . ($championship['location'] ?? ''));
        $pdf->addLine('Categoría: ' . ($championship['category'] ?? '') . ' | Modalidad: ' . ($championship['modality'] ?? ''));
        $pdf->addSpacer();
        $pdf->addLine('Participantes (' . count($players) . '):');
        foreach ($players as $p) {
            $pdf->addLine(' - ' . $p['name'] . ' (' . ($p['club'] ?? 'Club') . ') Rank: ' . ($p['ranking'] ?? 'Prov.'));
        }
        $pdf->addSpacer();
        foreach ($groups as $group) {
            $pdf->addLine('Grupo ' . $group['name'] . ':');
            foreach ($group['players'] as $gp) {
                $pdf->addLine(' * ' . $gp['name'] . ' (' . $gp['club'] . ')');
            }
            $pdf->addSpacer();
        }
        $pdf->addLine('Partidos registrados:');
        foreach ($matches as $m) {
            $score = $this->formatScore($m);
            $pdf->addLine($m['player1_name'] . ' vs ' . $m['player2_name'] . ' | ' . strtoupper($m['stage']) . ' | ' . $score);
        }
        return $pdf->output();
    }

    public function csv(array $institution, array $championship, array $players, array $groups, array $matches): string
    {
        $lines = [];
        $lines[] = 'Institución;' . ($institution['name'] ?? '');
        $lines[] = 'Campeonato;' . ($championship['name'] ?? '') . ';Fecha;' . ($championship['start_date'] ?? '') . ';Ubicación;' . ($championship['location'] ?? '');
        $lines[] = '';
        $lines[] = 'Participantes';
        $lines[] = 'Nombre;Club;Ranking';
        foreach ($players as $p) {
            $lines[] = sprintf('%s;%s;%s', $p['name'], $p['club'], $p['ranking'] ?? 'Prov.');
        }
        $lines[] = '';
        foreach ($groups as $group) {
            $lines[] = 'Grupo ' . $group['name'];
            foreach ($group['players'] as $gp) {
                $lines[] = sprintf('%s;%s;%s', $gp['name'], $gp['club'], $gp['ranking'] ?? '');
            }
        }
        $lines[] = '';
        $lines[] = 'Partidos;Etapa;Marcador';
        foreach ($matches as $m) {
            $lines[] = sprintf('%s vs %s;%s;%s', $m['player1_name'], $m['player2_name'], $m['stage'], $this->formatScore($m));
        }
        return implode("\n", $lines);
    }

    private function formatScore(array $match): string
    {
        if (empty($match['sets_json'])) {
            return 'Sin resultado';
        }
        $sets = json_decode($match['sets_json'], true);
        $map = array_map(function ($s) {
            return $s[0] . '-' . $s[1];
        }, $sets);
        return implode(', ', $map);
    }
}
