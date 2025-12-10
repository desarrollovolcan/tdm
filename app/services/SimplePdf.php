<?php
class SimplePdf
{
    private array $lines = [];

    public function addLine(string $text): void
    {
        $this->lines[] = $text;
    }

    public function addSpacer(): void
    {
        $this->lines[] = '';
    }

    public function output(): string
    {
        $stream = "BT /F1 11 Tf 14 TL 50 780 Td";
        foreach ($this->lines as $index => $line) {
            $escaped = $this->escapeText($line);
            $stream .= ($index === 0 ? '' : ' T*') . " ($escaped) Tj";
        }
        $stream .= ' ET';

        $contentLength = strlen($stream);
        $objects = [];
        $objects[] = '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj';
        $objects[] = '2 0 obj << /Type /Pages /Count 1 /Kids [3 0 R] >> endobj';
        $objects[] = '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >> endobj';
        $objects[] = "4 0 obj << /Length $contentLength >> stream\n$stream\nendstream endobj";
        $objects[] = '5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj';

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj . "\n";
        }
        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= "trailer << /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n$xrefOffset\n%%EOF";
        return $pdf;
    }

    private function escapeText(string $text): string
    {
        $replacements = [
            '\\' => '\\\\',
            '(' => '\\(',
            ')' => '\\)',
            "\r" => ' ',
            "\n" => ' ',
        ];
        return strtr($text, $replacements);
    }
}
