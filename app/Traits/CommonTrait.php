<?php

namespace App\Traits;
use App\Models\Setting;
use Carbon\Carbon;

trait CommonTrait {

    public function getYear(){
        $currentMonth = date('n');
        $currentYear = date('Y');
        if ($currentMonth >= 4) {
            $startYear = $currentYear % 100;
            $endYear = ($currentYear + 1) % 100;
        } else {
            $startYear = ($currentYear - 1) % 100;
            $endYear = $currentYear % 100;
        }
        return '/' . $startYear . '-' . $endYear;
    }

    private function sanitizeFilename($filename) {
        // Replace spaces with underscores
        $sanitizedFilename = str_replace(' ', '_', $filename);
        // Remove invalid characters
        $sanitizedFilename = preg_replace('/[<>:"\/\\\\|?*]+/', '', $sanitizedFilename);
        return $sanitizedFilename;
    }

    protected function getFinancialYears()
    {
        $currentYear = date('Y');
        $startYear = 2025; // or any start year you want
        $financialYears = [];
        for ($i = $currentYear + 1; $i >= $startYear; $i--) {
            $start = Carbon::createFromDate($i - 1, 4, 1)->format('d-m-Y');
            $end = Carbon::createFromDate($i, 3, 31)->format('d-m-Y');
            $id = ($i - 1) . '-' . $i; // e.g., 2024-2025
            $name = $start . ' to ' . $end;
            $financialYears[] = ['id' => $name, 'name' => $id];
        }
        return $financialYears;
    }

}
