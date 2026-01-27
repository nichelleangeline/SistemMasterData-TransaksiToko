<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class ImportCsvService
{
    public static function preview(
        string $path,
        array $requiredHeader,
        callable $validatorCallback,
        callable $duplicateCallback = null
    ): array {
        $file = fopen($path, 'r');
        $header = array_map('trim', fgetcsv($file));

        if ($header !== $requiredHeader) {
            throw new \Exception(
                'Header CSV salah. Harus: '.implode(',', $requiredHeader)
            );
        }

        $rows = [];
        $hasError = false;
        $line = 1;

        while (($row = fgetcsv($file)) !== false) {
            $line++;

            $data = array_combine($header, $row);

            $validator = $validatorCallback($data);
            $errors = [];

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
            }

            if ($duplicateCallback && $duplicateCallback($data)) {
                $errors[] = 'Duplicate dengan data existing';
            }

            $rows[] = array_merge($data, [
                'line'   => $line,
                'error'  => $errors ? implode(' | ', $errors) : null,
                'status' => $errors ? 'ERROR' : 'OK',
            ]);

            if ($errors) $hasError = true;
        }

        fclose($file);

        return [
            'rows'     => $rows,
            'hasError' => $hasError,
        ];
    }
}
